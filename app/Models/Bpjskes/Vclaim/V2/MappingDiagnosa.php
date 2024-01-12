<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingDiagnosa extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_diagnosa';
    protected $primaryKey = 'md_id';

    function getDiagnosaSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('md_simrs_diagnosa_kode')->where("md_bpjs_diagnosa_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->md_simrs_diagnosa_kode;
        }
        return NULL;
    }
}