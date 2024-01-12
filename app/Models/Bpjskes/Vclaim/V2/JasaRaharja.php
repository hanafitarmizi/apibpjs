<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class JasaRaharja extends Model
{
    // protected $connection = 'pgsql';
    // protected $table = 'bpjskes.lembar_pengajuan_klaim';
    // protected $primaryKey = 'id';
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function listSuplesi($req)
    {
        if($this->vclaim->setup([
            'method'=>'GET',
            'url'=>'sep/JasaRaharja/Suplesi/'.trim($req['no_kartu']).'/tglPelayanan/'.($req['tgl_pelayanan']!=NULL ? date('Y-m-d',strtotime($req['tgl_pelayanan'])) : date('Y-m-d'))
        ])->run()){
            $r=$this->vclaim->getResponse();
            foreach($r->jaminan as $q){
                $this->data[]=[
                    'no_register'=>$q->noRegister,
                    'no_sep'=>$q->noSep,
                    'no_sep_awal'=>$q->noSepAwal,
                    'no_surat_jaminan'=>$q->noSuratJaminan,
                    'tgl_kejadian'=>$q->tglKejadian!=NULL ? date('d-m-Y',strtotime($q->tglKejadian)) : NULL,
                    'tgl_sep'=>$q->tglSep!=NULL ? date('d-m-Y',strtotime($q->tglSep)) : NULL,
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
    function listIndukKecelakaan($req)
    {
        if($this->vclaim->setup([
            'method'=>'GET',
            'url'=>'sep/KllInduk/List/'.trim($req['no_kartu']),
        ])->run()){
            $r=$this->getResponse();
            foreach($r->jaminan as $q){

                $faskes=NULL;
                $r = new Referensi();
                if(!$r->faskes(1,$q->ppkPelSEP)){
                    $r->faskes(2,$q->ppkPelSEP);
                }
                $faskes=$r->data;

                $provinsi=NULL;
                if($r->provinsi()){
                    foreach($r->data as $d){
                        $a=explode('!#!',$d['id']);
                        if($a[0]==$q->kdProp){
                            $provinsi=$d;
                            break;
                        }
                    }
                }

                $kabupaten=NULL;
                if($r->kabupaten($q->kdProp)){
                    foreach($r->data as $d){
                        $a=explode('!#!',$d['id']);
                        if($a[0]==$q->kdKab){
                            $kabupaten=$d;
                            break;
                        }
                    }
                }

                $kecamatan=NULL;
                if($r->kecamatan($q->kdKab)){
                    foreach($r->data as $d){
                        $a=explode('!#!',$d['id']);
                        if($a[0]==$q->kdKec){
                            $kecamatan=$d;
                            break;
                        }
                    }
                }

                $this->data[]=[
                    'no_sep'=>$q->noSEP,
                    'ppk_pelayanan'=>$faskes,
                    'tgl_kejadian'=>$q->tglKejadian!=NULL ? date('d-m-Y',strtotime($q->tglKejadian)) : NULL,
                    'ket'=>$q->ketKejadian,
                    'no_suplesi'=>explode(',',$q->noSEPSuplesi),
                    'provinsi'=>$provinsi,
                    'kabupaten'=>$kabupaten,
                    'kecamatan'=>$kecamatan,
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
}