<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\SPRI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SpriController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rules();
    }
    /**
     * dokter = kode_namadokter
     * poliklinik = kode_namapoli
     */
    function rules()
    {
        $this->rules=[
            'no_pasien'=>'required',
            'no_kartu'=>'required',
            'dokter'=>'required',
            'poliklinik'=>'required',
            'tgl_kontrol'=>'required|date_format:d-m-Y',
            'user'=>'required',
        ];
    }
    function save(Request $req)
    {
        $v= Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $s = new SPRI();
            if($s->insertData($req->all())){
                $result=jsonResponse(true,'SPRI berhasil disimpan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function update(Request $req)
    {
        $this->rules['id']='required';
        $this->rules['no_spri']='required';
        $v= Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $s = new SPRI();
            if($s->updateData($req->all())){
                $result=jsonResponse(true,'SPRI berhasil diperbaharui',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}