<?php
namespace App\Models\Bpjskes\Antrol\V2;
class Referensi extends BaseAntrol
{
    public $data,$error_msg,$vclaim_data,$db_data;
    function poliklinik()
    {
        if($this->setup([
            'url'=>'ref/poli',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp as $q){
                $this->data[]=['id'=>trim($q->kdpoli).'!#!'.trim($q->nmpoli),'nama'=>trim($q->nmpoli),'sub_id'=>trim($q->kdsubspesialis).'!#!'.trim($q->nmsubspesialis),'sub_nama'=>trim($q->nmsubspesialis)];
            }
            return true;
        }
        return false;
    }
    function dokter()
    {
        if($this->setup([
            'url'=>'ref/dokter',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp as $q){
                $this->data[]=['id'=>trim($q->kodedokter).'!#!'.trim($q->namadokter),'nama'=>trim($q->namadokter)];
            }
            return true;
        }
        return false;
    }
    function jadwalDokter($req)
    {
        $tmp=explode('!#!',trim($req['poliklinik']));
        
        $poli=NULL;
        if(count($tmp)==2){
            $poli=$tmp[0];
        }else{
            $this->error_msg="Format poliklinik tidak valid";
            return false;
        }

        if($this->setup([
            'url'=>'jadwaldokter/kodepoli/'.$poli.'/tanggal/'.date('Y-m-d',strtotime(trim($req['tanggal']))),
            'method'=>'GET'
        ])->runJadwalDokter()){
            $tmp=(array) $this->getResponse();
            if(count($tmp)>0){
                foreach($tmp as $q){
                    $this->data[]=[
                        'poliklinik'=>[
                            'id'=>trim($q->kodepoli).'!#!'.trim($q->namapoli),
                            'nama'=>trim($q->namapoli),
                        ],
                        'sub_poliklinik'=>[
                            'id'=>trim($q->kodesubspesialis).'!#!'.trim($q->namasubspesialis),
                            'nama'=>trim($q->namasubspesialis),
                        ],
                        'hari'=>[
                            'id'=>$q->hari,
                            'nama'=>$q->namahari,
                        ],
                        'jadwal'=>$q->jadwal,
                        'dokter'=>[
                            'id'=>trim($q->kodedokter).'!#!'.trim($q->namadokter),
                            'nama'=>trim($q->namadokter)
                        ],
                        'kapasitas'=>$q->kapasitaspasien,
                        'libur'=>$q->libur
                    ];
                }
                return true;
            }else{
                $this->error_msg="Jadwal dokter tidak ditemukan";
                return false;
            }
        }
        return false;
    }
    function validateData($req)
    {
        $var = ['poliklinik'=>'Poliklinik','dokter'=>'Dokter'];
        foreach($var as $k => $v){
            if(isset($req[$k])){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg='Format '.$v.' tidak valid';
                    return false;
                }
            }
        }

        //check param jadwal
        $jadwal=implode('',array_keys($req['jadwal'][0]));
        if($jadwal!='haribukatutup'){
            $this->error_msg="Format jadwal tidak valid (input : hari, buka, tutup)";
            return false;
        }
        return true;
    }
    function prepareData($req)
    {
        $poli=explode('!#!',$req['poliklinik']);
        $subpoli=explode('!#!',$req['sub_poliklinik']);
        $dokter=explode('!#!',$req['dokter']);
        $jadwal=$req['jadwal'];
        $this->vclaim_data=[
            'kodepoli'=>$poli[0],
            'kodesubspesialis'=>$subpoli[0],
            'kodedokter'=>$dokter[0],
            'jadwal'=>$jadwal,
        ];
    }
    function updateJadwalDokter($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            if($this->setup([
                'url'=>'jadwaldokter/updatejadwaldokter',
                'method'=>'POST',
                'param'=>$this->vclaim_data,
            ])->run()){
                $this->data=$this->getResponse();
                return true;
            }
            return false;
        }
        return false;
    }
}