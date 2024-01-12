<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\RujukanKhusus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RujukanKhususController extends BaseController
{
    function list(Request $req)
    {
        $v=Validator::make($req->all(),[
            'bulan'=>'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'tahun'=>'required|integer|digits:4',
        ]);
        if(!$v->fails()){
            $r= new RujukanKhusus();
            if($r->listRujukan($req->all())){
                $result=jsonResponse(true,'List rujukan khusus berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function save(Request $req)
    {
        $v=Validator::make($req->all(),[
            'no_pasien'=>'required',
            'no_daftar'=>'required',
            'no_rujukan'=>'required',
            'diagnosa_primer'=>'required',
            'diagnosa_sekunder'=>'required',
            'prosedur'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new RujukanKhusus();
            if($r->saveRujukan($req->all())){
                $result=jsonResponse(true,'Rujukan khusus berhasil disimpan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function delete(Request $req)
    {
        $v=Validator::make($req->all(),[
            'id_rujukan'=>'required',
            'no_rujukan'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new RujukanKhusus();
            if($r->deleteRujukan($req->all())){
                $result=jsonResponse(true,'Rujukan khusus berhasil dihapus',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}