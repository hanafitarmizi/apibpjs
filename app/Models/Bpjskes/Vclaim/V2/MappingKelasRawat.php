<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingKelasRawat extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_kelas_rawat';
    protected $primaryKey = 'mkr_id ';

    function getKelasRawatSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mkr_simrs_kelas_rawat_kode')->where("mkr_bpjs_kelas_rawat_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mkr_simrs_kelas_rawat_kode;
        }
        return NULL;
    }
}