<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\Rujukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RujukanController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rules();
    }
    function rules()
    {
        $req= new Request();

        $this->rules=[
            'no_pasien'=>'required',
            'no_daftar'=>'required',
            'no_sep'=>'required',
            'tgl_rujukan'=>'required|date_format:d-m-Y',
            'tgl_kunjungan'=>'required|date_format:d-m-Y',
            'ppk_dirujuk_tingkat'=>'required',
            'ppk_dirujuk'=>'required',
            'jenis_layanan'=>'required|in:1,2',
            'catatan'=>'required',
            'diagnosa'=>'required',
            'tipe_rujukan'=>'required|in:0,1,2',
            'user'=>'required',
        ];

        if(request('jenis_layanan')==2){
            $this->rules['poli_rujukan']='required';
        }
    }
    function searchRujukan(Request $req)
    {
        $v=Validator::make($req->all(),[
            'jenis_faskes'=>'required|in:1,2',
            'jenis_cari'=>'required|in:1,2,3', // 1:no rujukan, 2:no kartu, 3:list
            'nomor'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->searchRujukan($req->input('jenis_faskes'),$req->input('jenis_cari'),$req->input('nomor'))){
                $result=jsonResponse(true,'Rujukan berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function rujukanKeluar(Request $req)
    {
        $v=Validator::make($req->all(),[
            'no_rujukan'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->searchRujukanKeluar($req->input('no_rujukan'))){
                $result=jsonResponse(true,'Rujukan keluar berhasil ditemukan',$r->data);
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
        $v=Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->saveRujukan($req->all())){
                $result=jsonResponse(true,'Rujukan berhasil disimpan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function update(Request $req)
    {
        $this->rules['id']='required|integer';
        $this->rules['no_rujukan']='required';
        $v=Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->updateRujukan($req->all())){
                $result=jsonResponse(true,'Rujukan berhasil diperbaharui',$r->data);
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
            'id'=>'required',
            'no_rujukan'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->deleteRujukan($req->all())){
                $result=jsonResponse(true,'Rujukan berhasil diperbaharui',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function listSpesialis(Request $req)
    {
        $v=Validator::make($req->all(),[
            'ppk_rujukan'=>'required',
            'tgl_rujukan'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->getListSpesialis($req->input('ppk_rujukan'),$req->input('tgl_rujukan'))){
                $result=jsonResponse(true,'List spesialis berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function listSarana(Request $req)
    {
        $v=Validator::make($req->all(),[
            'ppk_rujukan'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Rujukan();
            if($r->getListSarana($req->input('ppk_rujukan'))){
                $result=jsonResponse(true,'List sarana berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}