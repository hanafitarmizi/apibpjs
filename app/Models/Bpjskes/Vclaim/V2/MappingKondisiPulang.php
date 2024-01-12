<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingKondisiPulang extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_kondisi_pulang';
    protected $primaryKey = 'mkp_id';

    function getKondisiPulangSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mkp_simrs_kondisi_pulang_kode')->where("mkp_bpjs_kondisi_pulang_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mkp_simrs_kondisi_pulang_kode;
        }
        return NULL;
    }
}