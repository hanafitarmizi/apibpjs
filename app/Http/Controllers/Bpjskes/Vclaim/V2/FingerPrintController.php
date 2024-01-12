<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\FingerPrint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class FingerPrintController extends BaseController
{
    function getData(Request $req)
    {
        $v = Validator::make($req->all(),[
            'no_kartu'=>'required',
            'tgl_layanan'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new FingerPrint();
            if($s->getData($req->input('no_kartu'),$req->input('tgl_layanan'))){
                $result=jsonResponse(true,$s->error_msg);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getList(Request $req)
    {
        $v = Validator::make($req->all(),[
            'tgl_layanan'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new FingerPrint();
            if($s->getListFinger($req->input('tgl_layanan'))){
                $result=jsonResponse(true,'List finger print berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}