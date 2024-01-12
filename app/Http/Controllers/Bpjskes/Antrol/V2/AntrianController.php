<?php
namespace App\Http\Controllers\Bpjskes\Antrol\V2;
use App\Models\Bpjskes\Antrol\V2\Antrian;
use App\Models\Bpjskes\Antrol\V2\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class AntrianController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->setRules();
    }
    function setRules()
    {
        $this->rules=[
            'kode'=>'required',
            'jenis_pasien'=>'required|in:1,2', //1 = JKN,2=NON JKN
            'no_pasien'=>'required',
            'nik'=>'required',
            'no_hp'=>'required',
            'poliklinik'=>'required',
            'pasien_baru'=>'required',
            'tgl_periksa'=>'required|date_format:d-m-Y',
            'dokter'=>'required',
            'jam_praktek'=>'required',
            'jenis_kunjungan'=>'required|in:'.implode(',',array_keys(Params::JENIS_KUNJUNGAN)),
            'no_antrian'=>'required',
            'angka_antrian'=>'required',
            'estimasi_dilayani'=>'required',
            'kuota_jkn'=>'required',
            'sisa_kuota_jkn'=>'required',
            'kuota_non_jkn'=>'required',
            'sisa_kuota_non_jkn'=>'required',
            'ket'=>'required'
        ];

        if(request('jenis_pasien')==1){
            $this->rules['no_referensi']='required'; //no rujukan/no surat kontrol
            $this->rules['no_kartu']='required';
        }
    }
    function save(Request $req)
    {
        $v = Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $s = new Antrian();
            if($s->save($req->all())){
                $result=jsonResponse(true,'Antrian berhasil ditambah');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function updateWaktu(Request $req)
    {
        $v = Validator::make($req->all(),[
            'kode'=>'required',
            'aktifitas'=>'required',
            'waktu'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Antrian();
            if($s->updateWaktu($req->all())){
                $result=jsonResponse(true,'Data berhasil diupdate');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function batalAntrian(Request $req)
    {
        $v = Validator::make($req->all(),[
            'kode'=>'required',
            'ket'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Antrian();
            if($s->batalAntrian($req->all())){
                $result=jsonResponse(true,'Antrian berhasil dibatalkan');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function taskList(Request $req)
    {
        $v = Validator::make($req->all(),[
            'kode'=>'required',
        ]);
        if(!$v->fails()){
            $s = new Antrian();
            if($s->taskList($req->all())){
                $result=jsonResponse(true,'Data ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}