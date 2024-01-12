<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\PengajuanKlaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PengajuanKlaimController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rules();
    }
    function getData(Request $req)
    {
        $v = Validator::make($req->all(),[
            'tgl_masuk'=>'required|date_format:d-m-Y',
            'jenis_layanan'=>'required|integer|in:1,2'
        ]);
        if(!$v->fails()){
            $tgl_masuk=$req->input('tgl_masuk');
            $jenis_layanan=$req->input('jenis_layanan');
            $pk = new PengajuanKlaim();
            if($pk->getData($tgl_masuk,$jenis_layanan)){
                $result=jsonResponse(true,'LPK berhasil ditemukan',$pk->data);
            }else{
                $result=jsonResponse(false,$pk->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * diagnosa => [
     *  0=>['kode'=>'a01','level'=>'1']
     * ]
     * procedure=>[
     *  0=>['kode'=>'00.82']
     * ]
     */
    function save(Request $req)
    {
        $v = Validator::make($req->all(), $this->rules);
        if(!$v->fails()){
            $pk = new PengajuanKlaim();
            if($pk->insertData($req->all())){
                $result=jsonResponse(true,'LPK berhasil disimpan',$pk->data);
            }else{
                $result=jsonResponse(false,$pk->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function update(Request $req)
    {
        $this->rules['id']='required';
        $v = Validator::make($req->all(), $this->rules);
        if(!$v->fails()){
            $pk = new PengajuanKlaim();
            if($pk->updateData($req->all())){
                $result=jsonResponse(true,'LPK berhasil diperbaharui',$pk->data);
            }else{
                $result=jsonResponse(false,$pk->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function delete(Request $req)
    {
        $v = Validator::make($req->all(), [
            'id'=>'required|string',
            'no_sep'=>'required|string',
            'user'=>'required|string',
        ]);
        if(!$v->fails()){
            $pk = new PengajuanKlaim();
            if($pk->deleteData($req->all())){
                $result=jsonResponse(true,'LPK berhasil dihapus',$pk->data);
            }else{
                $result=jsonResponse(false,$pk->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * jaminan,poli,ruang_rawat,kelas_rawat,spesialis,cara_keluar,kondisi_pulang,ppk,poli_kontrol,dpjp = format : kode_nama
     * 
     */
    function rules()
    {
        $this->rules=[
            'no_pasien'=>'required|string|max:10',
            'no_daftar'=>'required|string|max:10',
            'no_sep'=>'required',
            'tgl_masuk'=>'required|date_format:d-m-Y',
            'tgl_keluar'=>'required|date_format:d-m-Y',
            'jaminan'=>'required',
            'poli'=>'required|string',
            'ruang_rawat'=>'required',
            'kelas_rawat'=>'required',
            'spesialis'=>'required',
            'cara_keluar'=>'required',
            'kondisi_pulang'=>'required',
            'tindak_lanjut'=>'required|integer|in:1,2,3,4',
            'ppk'=>'required',
            'tgl_kontrol'=>'required',
            'poli_kontrol'=>'required',
            'dpjp'=>'required',
            'user'=>'required',
            'diagnosa.*.kode'=>'required',
            'diagnosa.*.level'=>'required|in:1,2',
            'prosedur.*.kode'=>'required'
        ];
    }
}