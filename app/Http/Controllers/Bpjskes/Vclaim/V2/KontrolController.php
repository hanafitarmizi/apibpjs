<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\Params;
use App\Models\Bpjskes\Vclaim\V2\SuratKontrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class KontrolController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rulesKontrol();
    }
    function rulesKontrol()
    {
        $this->rules=[
            'no_pasien'=>'required|string',
            'no_daftar'=>'required|string',
            'debitur'=>'required',
            'no_sep'=>'required',
            'dokter'=>'required',
            'poliklinik'=>'required',
            'tgl_kontrol'=>'required|date_format:d-m-Y',
            'user'=>'required'
        ];
    }
    function save(Request $req)
    {
        $v = Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->insertKontrol($req->all())){
                $result=jsonResponse(true,'Surat kontrol berhasil disimpan',$s->data);
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
        $this->rules['id']='required';
        $this->rules['no_surat_kontrol']='required';
        $v = Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->updateKontrol($req->all())){
                $result=jsonResponse(true,'Surat kontrol berhasil diperbaharui',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function delete(Request $req)
    {
        $v = Validator::make($req->all(),[
            'id'=>'required|integer',
            'no_surat_kontrol'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->deleteKontrol($req->input('id'),$req->input('no_surat_kontrol'),$req->input('user'))){
                $result=jsonResponse(true,'Surat kontrol berhasil dihapus');
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function searchSep(Request $req)
    {
        $v = Validator::make($req->all(),[
            'no_sep'=>'required'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->searchSepKontrol($req->input('no_sep'))){
                $result=jsonResponse(true,'SEP surat kontrol berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function searchKontrol(Request $req)
    {
        $v = Validator::make($req->all(),[
            'nomor'=>'required'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->searchKontrol($req->input('nomor'))){
                $result=jsonResponse(true,'SEP surat kontrol berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function listKontrolByKartu(Request $req)
    {
        $v = Validator::make($req->all(),[
            'bulan'=>'required|in:'.implode(',',array_keys(Params::BULAN)),
            'tahun'=>'required',
            'no_kartu'=>'required',
            'jenis'=>'required'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->getListKontrolByKartu($req->input('bulan'),$req->input('tahun'),$req->input('no_kartu'),$req->input('jenis'))){
                $result=jsonResponse(true,'List surat kontrol berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function listKontrol(Request $req)
    {
        $v = Validator::make($req->all(),[
            'jenis_filter'=>'required|in:1,2',
            'tgl_awal'=>'required|date_format:d-m-Y',
            'tgl_akhir'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->getListKontrol($req->input('jenis_filter'),$req->input('tgl_awal'),$req->input('tgl_akhir'))){
                $result=jsonResponse(true,'List surat kontrol berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * jenis_kontrol = 1 : SPRI,2:rencana kontrol
     * nomor = jika jenis_kontrol 1 : nomor kartu, jika 2 : nomor sep
     */
    function listPoliklinik(Request $req)
    {
        $v = Validator::make($req->all(),[
            'jenis_kontrol'=>'required|in:1,2',
            'nomor'=>'required',
            'tgl_kontrol'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->getListPoliklinik($req->input('jenis_kontrol'),$req->input('nomor'),$req->input('tgl_kontrol'))){
                $result=jsonResponse(true,'List poliklinik berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * list dokter kontrol
     * jenis_kontrol = 1 : SPRI,2:rencana kontrol
     */
    function listDokter(Request $req)
    {
        $v = Validator::make($req->all(),[
            'jenis_kontrol'=>'required|in:1,2',
            'poliklinik'=>'required',
            'tgl_kontrol'=>'required|date_format:d-m-Y'
        ]);
        if(!$v->fails()){
            $s = new SuratKontrol();
            if($s->getListDokter($req->input('jenis_kontrol'),$req->input('poliklinik'),$req->input('tgl_kontrol'))){
                $result=jsonResponse(true,'List dokter berhasil ditemukan',$s->data);
            }else{
                $result=jsonResponse(false,$s->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}