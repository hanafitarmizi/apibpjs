<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FingerPrint extends Model
{
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function getData($no_kartu,$tgl)
    {
        if($this->vclaim->setup([
            'url'=>'SEP/FingerPrint/Peserta/'.trim($no_kartu).'/TglPelayanan/'.date('Y-m-d',strtotime(trim($tgl))),
            'method'=>'GET'
        ])->run()){
            $r=$this->vclaim->getResponse();
            if($r->kode==1){
                $this->error_msg=$r->status;
                return true;
            }else{
                $this->error_msg=$r->status;
                return false;
            }
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
    function getListFinger($tgl)
    {
        if($this->vclaim->setup([
            'url'=>'SEP/FingerPrint/List/Peserta/TglPelayanan/'.date('Y-m-d',strtotime(trim($tgl))),
            'method'=>'GET'
        ])->run()){
            $tmp=$this->vclaim->getResponse();
            foreach($tmp->list as $q){
                //get data peserta
                $peserta=NULL;
                if(!empty($q->noKartu)){
                    $p = new Peserta();
                    if($p->getdata($q->noKartu,2)){
                        $peserta=$p->data;
                    }
                }
                //get data sep
                $sep=NULL;
                // if(!empty($q->noSEP)){
                //     $s = new Sep();
                //     if($s->searchSep($q->noSEP)){
                //         $sep=$s->data;
                //     }
                // }
                $this->data[]=[
                    'nama'=>$peserta!=NULL ? $peserta['nama'] : '',
                    'no_pasien'=>$peserta!=NULL ? $peserta['no_pasien'] : '',
                    'no_kartu'=>$q->noKartu,
                    'no_rujukan'=>$sep!=NULL ? $sep['no_rujukan'] : '',
                    'no_sep'=>$q->noSEP,
                    'tgl_sep'=>$sep!=NULL ? $sep['tgl_sep'] : ''
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
}