<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingDpjp extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_dpjp';
    protected $primaryKey = 'mdp_id';

    function getDpjpSimrs($dpjp)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mdp_simrs_dpjp_kode')->where("mdp_bpjs_dpjp_kode","=",$dpjp)->first();
        if($unit!=NULL){
            return $unit->mdp_simrs_dpjp_kode;
        }
        return NULL;
    }
}