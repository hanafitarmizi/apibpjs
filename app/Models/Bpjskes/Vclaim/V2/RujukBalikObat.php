<?php
namespace App\Models\Bpjskes\Vclaim\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class RujukBalikObat extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_rujuk_balik_obat';
    protected $primaryKey = 'rbo_id';
    public $vlciam;
    function insert($id,$obat)
    {
        if(count($obat)>0){
            DB::table($this->table)->where('rbo_rujuk_balik_id',$id)->delete();
            foreach($obat as $d){
                $o=explode('!#!',$d['obat']);
                DB::table($this->table)->insert([
                    'rbo_rujuk_balik_id'=>$id,'rbo_bpjs_obat_kode'=>$o[0],'rbo_bpjs_obat_nama'=>$o[1],'rbo_signa1'=>$d['signa1'],'rbo_signa2'=>$d['signa2'],'rbo_jml_obat'=>$d['jml']
                ]);
            }
        }
    }
}