<?php
namespace App\Models\Bpjskes\Aplicare\V2;
class Ruang extends BaseAplicare
{
    public $data,$error_msg,$vclaim_data,$db_data;
    function list($req)
    {
        if($this->setup([
            'url'=>'rest/bed/read/'.env('BPJS_PPK_KODE').'/'.trim($req['start']).'/'.trim($req['limit']),
            'method'=>'GET',
        ])->run()){
            $this->data=(array) $this->getResponse();
            return true;
        }
        return false;
    }
    function validateData($req)
    {
        $var = ['kelas'=>'Kelas','ruang'=>'Ruang'];
        foreach($var as $k => $v){
            $tmp=explode('!#!',$req[$k]);
            if(count($tmp)!=2){
                $this->error_msg='Format '.$v.' tidak valid';
                return false;
            }
        }
    }
    function prepareData($req)
    {
        $kelas=explode('!#!',$req['kelas']);
        $ruang=explode('!#!',$req['ruang']);
        $this->vclaim_data=[
            'kodekelas'=>$kelas[0],
            'koderuang'=>$ruang[0],
            'namaruang'=>$ruang[1],
            'kapasitas'=>$req['kapasitas'],
            'tersedia'=>$req['tersedia'],
            'tersediapria'=>$req['tersedia_pria'],
            'tersediawanita'=>$req['tersedia_wanita'],
            'tersediapriawanita'=>$req['tersedia_pria']+$req['tersedia_wanita']
        ];
    }
    function save($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            if($this->setup([
                'url'=>'rest/bed/create/'.env('BPJS_PPK_KODE'),
                'method'=>'POST',
                'param'=>$this->vclaim_data,
            ])->run()){
                $this->data="Berhasil !!";
                return true;
            }
        }
        return false;
    }
    function update($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            if($this->setup([
                'url'=>'rest/bed/update/'.env('BPJS_PPK_KODE'),
                'method'=>'POST',
                'param'=>$this->vclaim_data,
            ])->run()){
                $this->data="Berhasil !!";
                return true;
            }
        }
        return false;
    }
    function delete($req)
    {
        if($this->validateData($req)){
            $kelas=explode('!#!',$req['kelas']);
            $ruang=explode('!#!',$req['ruang']);
            if($this->setup([
                'url'=>'rest/bed/delete/'.env('BPJS_PPK_KODE'),
                'method'=>'POST',
                'param'=>[
                    'kodekelas'=>$kelas[0],
                    'koderuang'=>$ruang[0],
                ],
            ])->run()){
                $this->data="Berhasil !!";
                return true;
            }
        }
        return false;
    }
}