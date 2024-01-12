<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class InaCbg extends Model
{
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function getData($no_sep)
    {
        if($this->vclaim->setup([
            'url'=>'sep/cbg/'.trim($no_sep),
            'method'=>'GET'
        ])->run()){
            $r=$this->vclaim->getResponse();
            foreach($r->pesertasep as $q){
                $this->data[]=[
                    'no_kartu'=>$q->noKartuBpjs,
                    'no_pasien'=>$q->noMr,
                    'no_rujukan'=>$q->noRujukan,
                    'jkel'=>$q->kelamin=='P' ? 'Perempuan' : 'Laki-laki',
                    'kelas_rawat'=>$q->klsRawat,
                    'tgl_lahir'=>$q->tglLahir!=NULL ? date('d-m-Y',strtotime($q->tglLahir)) : NULL,
                    'tgl_layanan'=>$q->tglPelayanan!=NULL ? date('d-m-Y',strtotime($q->tglPelayanan)) : NULL,
                    'tingkat_pelayanan'=>$q->tktPelayanan
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
}