<?php
namespace App\Models\Bpjskes\Vclaim\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class MappingPoli extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_poli';
    protected $primaryKey = 'mpl_id';

    function getPoliSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mpl_simrs_poli_kode')->where("mpl_bpjs_poli_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mpl_simrs_poli_kode;
        }
        return NULL;
    }
}