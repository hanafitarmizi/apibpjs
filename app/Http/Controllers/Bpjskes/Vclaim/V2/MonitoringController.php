<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;

use App\Models\Bpjskes\Vclaim\V2\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class MonitoringController extends BaseController
{
    /**
     * tgl = format d-m-Y
     * jenis = 1:rawat inap,2:rawat jalan
     */
    function kunjungan(Request $req)
    {
        $v = Validator::make($req->all(), [
            'tanggal'=>'required|date_format:d-m-Y',
            'jenis_layanan'=>'required|in:1,2'
        ]);
        if(!$v->fails()){
            $tgl=$req->input('tanggal');
            $jenis=$req->input('jenis_layanan');
            $m = new Monitoring();
            if($m->kunjungan($tgl,$jenis)){
                $result=jsonResponse(true,'Data kunjungan ditemukan',$m->getResponse());
            }else{
                $result=jsonResponse(false,$m->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function klaim(Request $req)
    {
        $v = Validator::make($req->all(), [
            'tgl_pulang'=>'required|date_format:d-m-Y',
            'jenis_layanan'=>'required|in:1,2',
            'status_klaim'=>'required|in:1,2,3',
        ]);
        if(!$v->fails()){
            $tgl_pulang=$req->input('tgl_pulang'); 
            $jenis_layanan=$req->input('jenis_layanan');
            $status_klaim=$req->input('status_klaim');
            $v = new Monitoring();
            if($v->klaim($tgl_pulang,$jenis_layanan,$status_klaim)){
                $result=jsonResponse(true,'Data klaim ditemukan',$v->data);
            }else{
                $result=jsonResponse(false,$v->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function historiPelayanan(Request $req)
    {
        $v = Validator::make($req->all(), [
            'no_kartu'=>'required',
            'tgl_mulai'=>'required|date_format:d-m-Y',
            'tgl_akhir'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $no_kartu=$req->input('no_kartu'); 
            $tgl_mulai=$req->input('tgl_mulai');
            $tgl_akhir=$req->input('tgl_akhir');
            $v = new Monitoring();
            if($v->historipelayanan($no_kartu,$tgl_mulai,$tgl_akhir)){
                $result=jsonResponse(true,'Data histori pelayanan ditemukan',$v->data);
            }else{
                $result=jsonResponse(false,$v->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function klaimJasaRaharja(Request $req)
    {
        $v = Validator::make($req->all(), [
            'jenis_layanan'=>'required|in:1,2',
            'tgl_mulai'=>'required|date_format:d-m-Y',
            'tgl_akhir'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $jenis_layanan=$req->input('jenis_layanan');
            $tgl_mulai=$req->input('tgl_mulai');
            $tgl_akhir=$req->input('tgl_akhir');
            $v = new Monitoring();
            if($v->klaimjasaraharja($jenis_layanan,$tgl_mulai,$tgl_akhir)){
                $result=jsonResponse(true,'Data histori pelayanan ditemukan',$v->data);
            }else{
                $result=jsonResponse(false,$v->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}