<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PengajuanKlaimProsedur extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'bpjskes.lembar_pengajuan_klaim_prosedur';
    protected $primaryKey = 'id';
    function insert($id,$prosedur)
    {
        if(count($prosedur)>0){
            DB::table($this->table)->where('lpk_id',$id)->delete();
            foreach($prosedur as $d){
                $p=explode('!#!',$d['id']);
                DB::table($this->table)->insert([
                    'lpk_id'=>$id,'prosedur_kode'=>$p[0],'prosedur_nama'=>$p[1]
                ]);
            }
        }
    }
}