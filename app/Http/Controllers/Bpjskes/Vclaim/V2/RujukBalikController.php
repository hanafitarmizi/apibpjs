<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;

use App\Models\Bpjskes\Vclaim\V2\RujukBalik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RujukBalikController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rules();
    }
    function searchByNomor(Request $req)
    {
        $v=Validator::make($req->all(),[
            'no_srb'=>'required',
            'no_sep'=>'required'
        ]);
        if(!$v->fails()){
            $no_srb=trim($req->input('no_srb'));
            $no_sep=trim($req->input('no_sep'));
            $br = new RujukBalik();
            if($br->searchByNomor($no_srb,$no_sep)){
                $result=jsonResponse(true,'Pencarian PRB berhasil ditemukan',$br->data);
            }else{
                $result=jsonResponse(false,$br->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function searchByTgl(Request $req)
    {
        $v=Validator::make($req->all(),[
            'tgl_mulai'=>'required|date_format:d-m-Y',
            'tgl_akhir'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $tgl_mulai=trim($req->input('tgl_mulai'));
            $tgl_akhir=trim($req->input('tgl_akhir'));
            $br = new RujukBalik();
            if($br->searchByTgl($tgl_mulai,$tgl_akhir)){
                $result=jsonResponse(true,'Pencarian PRB berhasil ditemukan',$br->data);
            }else{
                $result=jsonResponse(false,$br->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * obat = ['kode','signa1','signa2','jml_obat']
     */
    function save(Request $req)
    {
        $v=Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $br = new RujukBalik();
            if($br->insertData($req->all())){
                $result=jsonResponse(true,'Rujuk balik berhasil disimpan',$br->data);
            }else{
                $result=jsonResponse(false,$br->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function update(Request $req)
    {
        $this->rules['id']='required|integer';
        $this->rules['no_srb']='required';
        $v=Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $br = new RujukBalik();
            if($br->updateData($req->all())){
                $result=jsonResponse(true,'Rujuk balik berhasil diperbaharui',$br->data);
            }else{
                $result=jsonResponse(false,$br->error_msg);
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
            'no_srb'=>'required',
            'no_sep'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $br = new RujukBalik();
            if($br->deleteData($req->all())){
                $result=jsonResponse(true,'Rujuk balik berhasil dihapus',$br->data);
            }else{
                $result=jsonResponse(false,$br->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function rules()
    {
        $this->rules=[
            'no_pasien'=>'required|string',
            'no_daftar'=>'required|string',
            'no_sep'=>'required',
            'no_kartu'=>'required',
            'alamat'=>'required',
            'email'=>'required|email:rfc,dns',
            'program'=>'required|string',
            'dpjp'=>'required|string',
            'keterangan'=>'required',
            'saran'=>'required',
            'obat'=>'required',
            'user'=>'required',
        ];
    }
}