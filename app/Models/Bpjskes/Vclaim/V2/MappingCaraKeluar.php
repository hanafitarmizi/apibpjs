<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingCaraKeluar extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_mapping_cara_keluar';
    protected $primaryKey = 'mck_id';

    function getCaraKeluarSimrs($unit)
    {
        $unit=DB::connection($this->connection)->table($this->table)->select('mck_simrs_cara_keluar_kode')->where("mck_bpjs_cara_keluar_kode","=",$unit)->first();
        if($unit!=NULL){
            return $unit->mck_simrs_cara_keluar_kode;
        }
        return NULL;
    }
}