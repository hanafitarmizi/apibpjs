<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PengajuanSep extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_sep_pengajuan';
    protected $primaryKey = 'psp_id';
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function insertPengajuan($req)
    {
        DB::beginTransaction();
            try{
                $insert = DB::table($this->table)->insertGetId([
                    'psp_pasien_kode'=>$req['no_pasien'],
                    'psp_no_kartu'=>$req['no_kartu'],
                    'psp_tgl_sep'=>date('Y-m-d',strtotime($req['tgl_sep'])),
                    'psp_jenis_pelayanan'=>$req['jenis_layanan'],
                    'psp_jenis_pengajuan'=>$req['jenis_pengajuan'],
                    'psp_status'=>1,
                    'psp_ket_pengajuan'=>$req['keterangan'],
                    'psp_created_at'=>date('Y-m-d H:i:s'),
                    'psp_created_by'=>$req['user'],
                ]);
                if($insert){
                    if($this->vclaim->setup([
                        'url'=>'Sep/pengajuanSEP',
                        'method'=>'POST',
                        'param'=>[
                            'request'=>[
                                't_sep'=>[
                                    'noKartu'=>$req['no_kartu'],
                                    'tglSep'=>date('Y-m-d',strtotime($req['tgl_sep'])),
                                    'jnsPelayanan'=>$req['jenis_layanan'],
                                    'jnsPengajuan'=>$req['jenis_pengajuan'],
                                    'keterangan'=>$req['keterangan'],
                                    'user'=>setUser($req['user']),
                                ]
                            ]
                        ]
                    ])->run()){
                        $t=$this->vclaim->getResponse();
                        $this->data=$t;
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
        return false;
    }
    function approvalPengajuan($id,$user)
    {
        DB::beginTransaction();
            try{
                DB::table($this->table)->where('psp_id',$id)->update([
                    'psp_status'=>1,
                    'psp_updated_at'=>date('Y-m-d H:i:s'),
                    'psp_updated_by'=>$user
                ]);
                $m=DB::table($this->table)->where('psp_id',$id)->first();
                $params=[
                    'request'=>[
                        't_sep'=>[
                            'noKartu'=>$m->psp_no_kartu,
                            'tglSep'=>$m->psp_tgl_sep,
                            'jnsPelayanan'=>$m->psp_jenis_pelayanan,
                            'jnsPengajuan'=>$m->psp_jenis_pengajuan,
                            'keterangan'=>$m->psp_ket_pengajuan,
                            'user'=>setUser($user)
                        ]
                    ]
                ];
                if($this->vclaim->setup([
                    'url'=>'Sep/aprovalSEP',
                    'method'=>'POST',
                    'param'=>$params
                ])->run()){
                    $t=$this->vclaim->getResponse();
                    $this->data=$t;
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
        return false;
    }
}