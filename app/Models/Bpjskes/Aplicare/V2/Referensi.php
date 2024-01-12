<?php
namespace App\Models\Bpjskes\Aplicare\V2;
class Referensi extends BaseAplicare
{
    public $data,$error_msg,$vclaim_data,$db_data;
    function getKelas()
    {
        if($this->setup([
            'url'=>'rest/ref/poli',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kodekelas).'!#!'.trim($q->namakelas),'nama'=>trim($q->namakelas)];
            }
            return true;
        }else{
            return false;
        }
        return $result;
    }
}