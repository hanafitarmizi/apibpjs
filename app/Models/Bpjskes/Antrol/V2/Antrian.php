<?php
namespace App\Models\Bpjskes\Antrol\V2;
class Antrian extends BaseAntrol
{
    public $data,$error_msg,$vclaim_data,$db_data;
    function validateData($req)
    {
        $var = ['poliklinik'=>'Poliklinik','sub_poliklinik'=>'Sub Poliklinik','dokter'=>'Dokter'];
        foreach($var as $k => $v){
            if(isset($req[$k])){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg='Format '.$v.' tidak valid';
                    return false;
                }
            }
        }
    }
    function prepareData($req)
    {
        $poli=explode('!#!',$req['poliklinik']);
        $dokter=explode('!#!',$req['dokter']);
        $this->vclaim_data=[
            'kodebooking'=>$req['kode'],
            'jenispasien'=>$req['jenis_pasien']==1 ? 'JKN' : 'NON JKN',
            'nomorkartu'=>$req['jenis_pasien']==1 ? $req['no_kartu'] : '',
            'nik'=>$req['nik'],
            'nohp'=>$req['no_hp'],
            'kodepoli'=>$poli[0],
            'namapoli'=>$poli[1],
            'pasienbaru'=>$req['pasien_baru'],
            'norm'=>$req['no_pasien'],
            'tanggalperiksa'=>date('Y-m-d',strtotime($req['tgl_periksa'])),
            'kodedokter'=>$dokter[0],
            'namadokter'=>$dokter[1],
            'jampraktek'=>$req['jam_praktek'],
            'jeniskunjungan'=>$req['jenis_kunjungan'],
            'nomorreferensi'=>$req['jenis_pasien']==1 ? $req['no_referensi'] : '',
            'nomorantrean'=>$req['no_antrian'],
            'angkaantrean'=>$req['angka_antrian'],
            'estimasidilayani'=>$req['estimasi_dilayani'],
            'sisakuotajkn'=>$req['sisa_kuota_jkn'],
            'kuotajkn'=>$req['kuota_jkn'],
            'sisakuotanonjkn'=>$req['kuota_non_jkn'],
            'keterangan'=>$req['ket']
        ];
    }
    function save($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            if($this->setup([
                'url'=>'antrean/add',
                'method'=>'POST',
                'param'=>$this->vclaim_data,
            ])->run()){
                $this->data="Berhasil !!";
                return true;
            }
        }
        return false;
    }
    function updateWaktu($req)
    {
        if($this->setup([
            'url'=>'antrean/updatewaktu',
            'method'=>'POST',
            'param'=>[
                'kodebooking'=>$req['kode'],
                'taskid'=>$req['aktifitas'],
                'waktu'=>$req['waktu']
            ],
        ])->run()){
            $this->data="Berhasil !!";
            return true;
        }
        return false;
    }
    function batalAntrian($req)
    {
        if($this->setup([
            'url'=>'antrean/batal',
            'method'=>'POST',
            'param'=>[
                'kodebooking'=>$req['kode'],
                'keterangan'=>$req['ket'],
            ],
        ])->run()){
            $this->data="Berhasil !!";
            return true;
        }
        return false;
    }
    function taskList($req)
    {
        if($this->setup([
            'url'=>'antrean/getlisttask',
            'method'=>'POST',
            'param'=>[
                'kodebooking'=>$req['kode'],
            ],
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=[
                    'kode'=>$q->kodebooking,
                    'aktifitas'=>[
                        'id'=>$q->taskid,
                        'nama'=>$q->taskname,
                    ],
                    'waktu_mulai'=>$q->waktus,
                    'waktu_akhir'=>$q->waktu,
                ];
            }
            return true;
        }
        return false;
    }
}