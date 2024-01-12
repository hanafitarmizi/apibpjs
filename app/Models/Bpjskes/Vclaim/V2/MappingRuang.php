<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingRuang extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_ruang';
    protected $primaryKey = 'mpr_id';

    function getRuangSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mpr_simrs_ruang_kode')->where("mpr_bpjs_ruang_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mpr_simrs_ruang_kode;
        }
        return NULL;
    }
}