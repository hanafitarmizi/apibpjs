<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\InaCbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class InacbgController extends BaseController
{
    function getData(Request $req)
    {
        $v = Validator::make($req->all(),[
            'no_sep'=>'required'
        ]);
        if(!$v->fails()){
            $s = new InaCbg();
            if($s->getData($req->input('no_sep'))){
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