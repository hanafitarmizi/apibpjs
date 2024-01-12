<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;

use App\Models\Bpjskes\Vclaim\V2\Peserta;
use Illuminate\Http\Request;
class PesertaController extends BaseController
{
    function getpeserta(Request $req)
     {
    $type=$req->input('tipe');
    $id=$req->input('nomor');


         dd( new Peserta());
         $v = new Peserta();

         if($v->getdata($id,$type)){
             $result=jsonResponse(true,'Data peserta ditemukan',$v->data);
        }else{
             $result=jsonResponse(false,$v->error_msg);
         }
         $result=jsonResponse(true,'Data peserta ditemukan',$req);
         return  $result;
      }



    function allpeserta()
    {
        $v=New Peserta();
        dd($v);
    }

    // function getpeserta(Request $req)
    // {
    //     $type=$req->input('tipe');
    //     $id=$req->input('nomor');

    //         $result=jsonResponse(true,'Data peserta ditemukan',$req->input('tipe'));

    //     return $result;
    // }
}