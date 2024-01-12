<?php
namespace App\Http\Controllers\Bpjskes\Aplicare\V2;
use App\Models\Bpjskes\Aplicare\V2\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RuangController extends BaseController
{
    function listRuang(Request $req)
    {
        $v = Validator::make($req->all(),[
            'start'=>'required',
            'limit'=>'required'
        ]);
        if(!$v->fails()){
            $s = new Ruang();
            if($s->list($req->all())){
                $result=jsonResponse(true,'Ruang berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function save(Request $req)
    {
        $v = Validator::make($req->all(),[
            'kelas'=>'required',
            'ruang'=>'required',
            'kapasitas'=>'required',
            'tersedia'=>'required',
            'tersedia_pria'=>'required',
            'tersedia_wanita'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Ruang();
            if($s->save($req->all())){
                $result=jsonResponse(true,'Ruang berhasil ditambah');
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
        $v = Validator::make($req->all(),[
            'kelas'=>'required',
            'ruang'=>'required',
            'kapasitas'=>'required',
            'tersedia'=>'required',
            'tersedia_pria'=>'required',
            'tersedia_wanita'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Ruang();
            if($s->update($req->all())){
                $result=jsonResponse(true,'Ruang berhasil diperbaharui');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function deleteRuang(Request $req)
    {
        $v = Validator::make($req->all(),[
            'kelas'=>'required',
            'ruang'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Ruang();
            if($s->delete($req->all())){
                $result=jsonResponse(true,'Ruang berhasil dihapus');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}