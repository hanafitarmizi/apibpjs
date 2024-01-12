<?php
namespace App\Models\Bpjskes\Vclaim\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class SPRI extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_spri';
    protected $primaryKey = 'spri_id';
    public $vclaim,$error_msg,$vclaim_data,$db_data;
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function validateData($req)
    {
        $var = ['dokter'=>'Dokter','poliklinik'=>'Poliklinik'];
        if(count($var)>0){
            foreach($var as $k => $v){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg='Format '.$v.' tidak valid';
                    return false;
                }
            }
        }
        return true;
    }
    function prepareData($req,$is_new=true)
    {
        $dokter=explode('!#!',$req['dokter']);
        $poli=explode('!#!',$req['poliklinik']);
        $unit_rs=(new MappingPoli())->getPoliSimrs($poli[0]);

        $this->vclaim_data=[
            'kodeDokter'=>$dokter[0],
            'poliKontrol'=>$poli[0],
            'tglRencanaKontrol'=>date('Y-m-d',strtotime($req['tgl_kontrol'])),
            'user'=>setUser($req['user']),
        ];

        $this->db_data=[
            'spri_pasien_kode'=>$req['no_pasien'],
            'spri_tgl_kontrol'=>date('Y-m-d',strtotime($req['tgl_kontrol'])),
            'spri_no_kartu'=>$req['no_kartu'],
            'spri_bpjs_dokter_kode'=>$dokter[0],
            'spri_bpjs_dokter_nama'=>$dokter[1],
            'spri_simrs_dokter_kode'=>(new MappingDpjp())->getDpjpSimrs($dokter[0]),
            'spri_bpjs_unit_kode'=>$poli[0],
            'spri_bpjs_unit_nama'=>$poli[1],
            'spri_simrs_unit_kode'=>$unit_rs
        ];

        if($is_new){
            $this->vclaim_data['noKartu']=$req['no_kartu'];
            $this->db_data['spri_created_at']=date('Y-m-d H:i:s');
            $this->db_data['spri_created_by']=$req['user'];
        }else{
            $this->vclaim_data['noSPRI']=$req['no_spri'];
            $this->db_data['spri_no_surat']=$req['no_spri'];
            $this->db_data['spri_updated_at']=date('Y-m-d H:i:s');
            $this->db_data['spri_updated_by']=$req['user'];
        }
    }
    function insertData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try{
                    $insert=DB::table($this->table)->insertGetId($this->db_data);
                    if($insert!=NULL){
                        if($this->vclaim->setup([
                            'url'=>'RencanaKontrol/InsertSPRI',
                            'method'=>'POST',
                            'param'=>[
                                'request'=>$this->vclaim_data,
                            ]
                        ])->run()){
                            $result=$this->vclaim->getResponse();
                            DB::table($this->table)->where('spri_id',$insert)->update(['spri_no_surat'=>$result->noSPRI]);
                            $this->data=['id'=>$insert,'nomor'=>$result->noSPRI];
                            DB::commit();
                            return true;
                        }else{
                            $this->error_msg=$this->vclaim->error_msg;
                            DB::rollBack();
                        }
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function updateData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req,false);
            DB::beginTransaction();
                try{
                    DB::table($this->table)->where('spri_id','=',$req['id'])->update($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'RencanaKontrol/UpdateSPRI',
                        'method'=>'PUT',
                        'param'=>['request'=>$this->vclaim_data],
                    ])->run()){
                        $result=$this->vclaim->getResponse();
                        $this->data=['id'=>$req['id'],'nomor'=>$result->noSPRI];
                        DB::commit();
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::rollBack();
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
}