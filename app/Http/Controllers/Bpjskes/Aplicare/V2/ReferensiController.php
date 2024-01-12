<?php
namespace App\Http\Controllers\Bpjskes\Aplicare\V2;
use App\Models\Bpjskes\Aplicare\V2\Referensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ReferensiController extends BaseController
{
    function getKelasRuang()
    {
        $r = new Referensi();
        if($r->getKelas()){
            $result=jsonResponse(true,'Kelas ruang berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
}