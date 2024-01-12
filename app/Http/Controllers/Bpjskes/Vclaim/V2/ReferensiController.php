<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\Referensi;
use App\Models\Bpjskes\Vclaim\V2\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
class ReferensiController extends BaseController
{
    function getDiagnosa(Request $req)
    {
        $v = Validator::make($req->all(), [
            'diagnosa'=>'required',
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->diagnosa($req->input('diagnosa'))){
                $result=jsonResponse(true,'Diagnosa berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getPoliklinik(Request $req)
    {
        $v = Validator::make($req->all(), [
            'poli'=>'required',
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->poliklinik($req->input('poli'))){
                $result=jsonResponse(true,'Poliklinik berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * jenis_faskes : 1. Faskes 1, 2. Faskes 2/RS
     */
    function getFaskes(Request $req)
    {
        $v = Validator::make($req->all(), [
            'faskes'=>'required',
            'jenis_faskes'=>'required',
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->faskes($req->input('jenis_faskes'),$req->input('faskes'))){
                $result=jsonResponse(true,'Fasilitas kesehatan berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getDpjp(Request $req)
    {
        $rules=[
            'jenis_layanan'=>'required|in:1,2',
            'spesialis'=>'required'
        ];
        if($req->input('tgl_layanan')!=NULL){
            $rules['tgl_layanan']='required|date_format:d-m-Y';
        }
        $v = Validator::make($req->all(),$rules);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->dpjp($req->all())){
                $result=jsonResponse(true,'DPJP berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getProvinsi(Request $req)
    {
        $r = new Referensi();
        if($r->provinsi()){
            $result=jsonResponse(true,'Provinsi berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getKabupaten(Request $req)
    {
        $v = Validator::make($req->all(), [
            'provinsi'=>'required'
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->kabupaten($req->input('provinsi'))){
                $result=jsonResponse(true,'Kabupaten berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getKecamatan(Request $req)
    {
        $v = Validator::make($req->all(), [
            'kabupaten'=>'required'
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->kecamatan($req->input('kabupaten'))){
                $result=jsonResponse(true,'Kecamatan berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getDiagnosaPrb()
    {
        $r = new Referensi();
        if($r->diagnosaprb()){
            $result=jsonResponse(true,'Diagnosa PRB berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getGenerikPrb(Request $req)
    {
        $v = Validator::make($req->all(), [
            'obat'=>'required'
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->generikprb($req->input('obat'))){
                $result=jsonResponse(true,'Obat generik PRB berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getTindakan(Request $req)
    {
        $v = Validator::make($req->all(), [
            'tindakan'=>'required'
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->tindakan($req->input('tindakan'))){
                $result=jsonResponse(true,'Tindakan/prosedur berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getKelasRawat()
    {
        $r = new Referensi();
        if($r->kelasrawat()){
            $result=jsonResponse(true,'Kelas rawat berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getDokter(Request $req)
    {
        $v = Validator::make($req->all(), [
            'dokter'=>'required'
        ]);
        if(!$v->fails()){
            $r = new Referensi();
            if($r->dokter($req->input('dokter'))){
                $result=jsonResponse(true,'Dokter berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getSpesialistik()
    {
        $r = new Referensi();
        if($r->spesialistik()){
            $result=jsonResponse(true,'Spesialistik berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getRuangRawat()
    {
        $r = new Referensi();
        if($r->ruangrawat()){
            $result=jsonResponse(true,'Ruang rawat berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getCaraKeluar()
    {
        $r = new Referensi();
        if($r->carakeluar()){
            $result=jsonResponse(true,'Cara keluar berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getPascaPulang()
    {
        $r = new Referensi();
        if($r->pascapulang()){
            $result=jsonResponse(true,'Pasca pulang berhasil ditemukan',$r->data);
        }else{
            $result=jsonResponse(false,$r->error_msg);
        }
        return $result;
    }
    function getTujuanKunjungan()
    {
        return jsonResponse(true,'Tujuan kunjungan berhasil ditemukan',Params::TUJUAN_KUNJUNGAN);
    }
    function getFlagProsedur()
    {
        return jsonResponse(true,'Flag prosedur berhasil ditemukan',Params::FLAG_PROSEDUR);
    }
    function getPembiayaan()
    {
        return jsonResponse(true,'Pembiayaan berhasil ditemukan',Params::PEMBIAYAAN);
    }
    function getPenunjang()
    {
        return jsonResponse(true,'Penunjang berhasil ditemukan',Params::PENUNJANG);
    }
    function getAsesmenPelayanan()
    {
        return jsonResponse(true,'Asesmen pelayanan berhasil ditemukan',Params::ASESMEN_PELAYANAN);
    }
    function getStatusKecelakaanKerja()
    {
        return jsonResponse(true,'Status kecelakaan kerja berhasil ditemukan',Params::STATUS_LAKA_LANTAS);
    }
    function getStatusPulangRawatinap()
    {
        return jsonResponse(true,'Status kecelakaan kerja berhasil ditemukan',Params::STATUS_PULANG_RAWATINAP);
    }
}