<?php
namespace App\Http\Controllers\Bpjskes\Antrol\V2;
use App\Models\Bpjskes\Antrol\V2\Referensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ReferensiController extends BaseController
{
    function getPoliklinik(Request $req)
    {
        $r = new Referensi();
        if($r->poliklinik()){
            $result=jsonResponse(true,'Poliklinik berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getDokter()
    {
        $r = new Referensi();
        if($r->dokter()){
            $result=jsonResponse(true,'Dokter berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getJadwalDokter(Request $req)
    {
        $v = Validator::make($req->all(),[
            'poliklinik'=>'required',
            'tanggal'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new Referensi();
            if($s->jadwalDokter($req->all())){
                $result=jsonResponse(true,'Jadwal dokter berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function updateJadwalDokter(Request $req)
    {
        $v = Validator::make($req->all(),[
            'poliklinik'=>'required',
            'sub_poliklinik'=>'required',
            'dokter'=>'required',
            'jadwal'=>'required'
        ]);
        if(!$v->fails()){
            $s = new Referensi();
            if($s->updateJadwalDokter($req->all())){
                $result=jsonResponse(true,'Jadwal dokter berhasil disimpan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}