<?php
namespace App\Models\Bpjskes\Vclaim\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class MappingPenunjang extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_penunjang';
    protected $primaryKey = 'mpj_id';

    function getPenunjangSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mpj_simrs_penunjang_kode')->where("mpj_bpjs_penunjang_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mpj_simrs_penunjang_kode;
        }
        return NULL;
    }
}