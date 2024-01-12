<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\JasaRaharja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class JasaRaharjaController extends BaseController
{
    function suplesi(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_kartu'=>'required',
            'tgl_pelayanan'=>'required|date_format:d-m-Y',
        ]);
        if(!$v->fails()){
            $r= new JasaRaharja();
            if($r->listSuplesi($req->all())){
                $result=jsonResponse(true,'Suplesi jasa raharja berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function dataIndukKecelakaan(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_kartu'=>'required'
        ]);
        if(!$v->fails()){
            $r= new JasaRaharja();
            if($r->listIndukKecelakaan($req->all())){
                $result=jsonResponse(true,'Suplesi jasa raharja berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}