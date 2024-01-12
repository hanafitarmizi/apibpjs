<?php
namespace App\Http\Controllers\Bpjskes\Vclaim\V2;
use App\Models\Bpjskes\Vclaim\V2\Sep;
use App\Models\Bpjskes\Vclaim\V2\Params;
use App\Models\Bpjskes\Vclaim\V2\Referensi;
use App\Models\Bpjskes\Vclaim\V2\PengajuanSep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class SepController extends BaseController
{
    public $rules;
    function __construct()
    {
        $this->rules();
    }
    function rules()
    {
        $this->rules=[
            'no_pasien'=>'required',
            'no_kartu'=>'required',
            'tgl_sep'=>'required|date_format:d-m-Y',
            'jenis_layanan'=>'required|integer|in:'.implode(',',array_keys(Params::JENIS_LAYANAN)),
            'ppk_rujukan_tingkat'=>'required|integer',
            'ppk_rujukan'=>'required',
            'tgl_rujukan'=>'required|date_format:d-m-Y',
            'diagnosa'=>'required',
            'cob'=>'required',
            'katarak'=>'required',
            'catatan'=>'required',
            'laka_lantas'=>'required|in:'.implode(',',array_keys(Params::STATUS_LAKA_LANTAS)),
            'no_telp'=>'required',
            'user'=>'required'
        ];
        
        if(request('jenis_layanan')==2){ //if rawat jalan
            $this->rules['poliklinik']='required';
            $this->rules['poliklinik_eksekutif']='required';
            $this->rules['tujuan_kunjungan']='required|in:'.implode(',',array_keys(Params::TUJUAN_KUNJUNGAN));

        }else{//jika rwt inap
            $this->rules['dpjp']='required';
            $this->rules['no_surat_kontrol']='required';
            $this->rules['no_rujukan']='required';
        }
        
        //jika naik kelas
        if(request('naik_kelas_rawat')!=NULL){
            $this->rules['pembiayaan']='required|in:'.implode(',',array_keys(Params::PEMBIAYAAN));
            if(request('pembiayaan')!=NULL){
                if(request('pembiayaan')==1){
                    $this->rules['penanggung_jawab']='required';
                }
            }
        }

        //flag prosedur & penunjang hanya diisi jika tujuan prosedur
        if(request('tujuan_kunjungan')==1){
            $this->rules['flag_prosedur']='required|in:0,1';
            $this->rules['penunjang']='required|in:'.implode(',',array_keys(Params::PENUNJANG));
        }

        //jika tujuan konsul
        if(request('tujuan_kunjungan')==2){
            $this->rules['asesmen_pelayanan']='required';
        }
        
        //jika kontrol
        if(request('asesmen_pelayanan')==5){
            $this->rules['no_surat_kontrol']='required';
        }

        //jika laka lantas
        if(request('laka_lantas')!=0){
            $this->rules['laka_lantas_tgl_kejadian']='required|date_format:d-m-Y';
            $this->rules['laka_lantas_ket']='required';
            $this->rules['laka_lantas_suplesi']='required|in:0,1';
            if(request('laka_lantas_suplesi')==1){//jika ada suplesi
                $this->rules['laka_lantas_no_suplesi']='required';
            }
            $this->rules['laka_lantas_prov']='required';
            $this->rules['laka_lantas_kab']='required';
            $this->rules['laka_lantas_kec']='required';
        }
    }
    function search(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_sep'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Sep();
            if($r->searchSep($req->input('no_sep'))){
                $result=jsonResponse(true,'Sep berhasil ditemukan',$r->data);
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
        $v= Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $r= new Sep();
            if($r->saveSep($req->all())){
                $result=jsonResponse(true,'Sep berhasil disimpan',$r->data);
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
        $this->rules['no_sep']='required';
        $v= Validator::make($req->all(),$this->rules);
        if(!$v->fails()){
            $r= new Sep();
            if($r->updateSep($req->all())){
                $result=jsonResponse(true,'Sep berhasil diperbaharui',$r->data);
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
        $v= Validator::make($req->all(),[
            'no_pasien'=>'required',
            'no_sep'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Sep();
            if($r->deleteSep($req->all())){
                $result=jsonResponse(true,'Sep berhasil dihapus',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    /**
     * no_pasien,no_sep,tgl_pulang,status_pulang,no_surat_meninggal, ,user
     */
    function updateSepPulang(Request $req)
    {
        //get list cara keluar
        $cara_keluar=NULL;
        $r = new Referensi();
        if($r->carakeluar()){
            $cara_keluar=array_map(function($q){
                $t=explode('!#!',$q['id']);
                return $t[0];
            },$r->data);
        }

        $rules['no_pasien']='required';
        $rules['no_sep']='required';
        $rules['tgl_pulang']='required|date_format:d-m-Y';
        $rules['status_pulang']='required|in:'.implode(',',array_keys(Params::STATUS_PULANG_RAWATINAP));
        if($req->input('status_pulang')==4){
            $rules['no_surat_meninggal']='required';
            $rules['tgl_meninggal']='required|date_format:d-m-Y';
        }
        $rules['user']='required';
        $v= Validator::make($req->all(),$rules);
        if(!$v->fails()){
            $r= new Sep();
            if($r->updateSepPulang($req->all())){
                $result=jsonResponse(true,'Tgl pulang SEP berhasil disimpan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function pengajuanSep(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_pasien'=>'required',
            'no_kartu'=>'required',
            'tgl_sep'=>'required|date_format:d-m-Y',
            'jenis_layanan'=>'required|in:1,2',
            'jenis_pengajuan'=>'required|in:1,2',
            'keterangan'=>'required',
            'user'=>'required',
        ]);
        if(!$v->fails()){
            $r= new PengajuanSep();
            if($r->insertPengajuan($req->all())){
                $result=jsonResponse(true,'Pengajuan SEP berhasil disimpan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function approvalPengajuanSep(Request $req)
    {
        $v= Validator::make($req->all(),[
            'id'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new PengajuanSep();
            if($r->approvalPengajuan($req->input('id'),$req->input('user'))){
                $result=jsonResponse(true,'Pengajuan SEP berhasil disimpan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function listSepInternal(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_sep'=>'required',
        ]);
        if(!$v->fails()){
            $r= new Sep();
            if($r->listSepInternal($req->input('no_sep'))){
                $result=jsonResponse(true,'SEP internal berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function deleteSepInternal(Request $req)
    {
        $v= Validator::make($req->all(),[
            'no_sep'=>'required',
            'no_surat'=>'required',
            'tgl_rujukan'=>'required|date_format:d-m-Y',
            'poliklinik'=>'required',
            'user'=>'required'
        ]);
        if(!$v->fails()){
            $r= new Sep();
            if($r->deleteSepInternal($req->all())){
                $result=jsonResponse(true,'SEP internal berhasil dihapus',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function searchPostRawatinap(Request $req)
    {
        $rules['no_kartu']='required';
        if($req->input('tgl_awal')!=NULL || $req->input('tgl_akhir')!=NULL){
            $rules['tgl_awal']='required|date_format:d-m-Y';
            $rules['tgl_akhir']='required|date_format:d-m-Y';
        }
        $v= Validator::make($req->all(),$rules);
        if(!$v->fails()){
            $r= new Sep();
            if($r->searchPostRawatinap($req->all())){
                $result=jsonResponse(true,'Post kontrol rawat inap berhasil ditemukan',$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
    function getSepIgd(Request $req)
    {
        $rules['no_kartu']='required';
        $v= Validator::make($req->all(),$rules);
        if(!$v->fails()){
            $r= new Sep();
            if($r->searchSepIgd($req->input('no_kartu'))){
                $result=jsonResponse(true,$r->error_msg,$r->data);
            }else{
                $result=jsonResponse(false,$r->error_msg);
            }
        }else{
            $result=jsonResponse(false,$v->errors());
        }
        return $result;
    }
}