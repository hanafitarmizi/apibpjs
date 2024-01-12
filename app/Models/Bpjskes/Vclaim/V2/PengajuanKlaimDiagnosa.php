<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PengajuanKlaimDiagnosa extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'bpjskes.lembar_pengajuan_klaim_diagnosa';
    protected $primaryKey = 'id';
    function insert($id,$diagnosa)
    {
        if(count($diagnosa)>0){
            DB::table($this->table)->where('lpk_id',$id)->delete();
            foreach($diagnosa as $d){
                $dd=explode('!#!',$d['id']);
                DB::table($this->table)->insert([
                    'lpk_id'=>$id,'diagnosa_kode'=>$dd[0],'diagnosa_nama'=>$dd[1],'level'=>$d['level']
                ]);
            }
        }
    }
}