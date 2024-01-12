<?php
namespace App\Http\Controllers\Bpjskes\Antrol\V2;
use App\Models\Bpjskes\Antrol\V2\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class DashboardController extends BaseController
{
    function rekapitulasiTanggal(Request $req)
    {
        $v = Validator::make($req->all(),[
            'tanggal'=>'required|date_format:d-m-Y',
            'jenis_waktu'=>'required|in:rs,server'
        ]);
        if(!$v->fails()){
            $s = new Dashboard();
            if($s->rekapTanggal($req->all())){
                $result=jsonResponse(true,'Data berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function rekapitulasiBulan(Request $req)
    {
        $v = Validator::make($req->all(),[
            'bulan'=>'required',
            'tahun'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Dashboard();
            if($s->rekapBulan($req->all())){
                $result=jsonResponse(true,'Data berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}