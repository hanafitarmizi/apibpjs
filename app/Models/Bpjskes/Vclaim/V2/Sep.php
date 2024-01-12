<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Sep extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_sep';
    protected $primaryKey = 'sep_id';

    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;

    public $pembiayaan=[1=>'Pribadi',2=>'Pemberi Kerja',3=>'Asuransi Kesehatan Tambahan'];
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function formatReturnSep($data)
    {
        $peserta=NULL;
        $v = new Peserta();
        if($v->getdata($data->peserta->noKartu,2)){
            $peserta=$v->data;
        }

        $rujukan=NULL;
        if(!empty($data->noRujukan)){
            $find_rujukan=true;
            $r= new Rujukan();
            if(!$r->searchRujukan(1,1,$data->noRujukan)){
                if(!$r->searchRujukan(2,1,$data->noRujukan)){
                    $find_rujukan=false;
                }
            }
            if($find_rujukan){
                $rujukan=$r->data;
            }
        }
        
        

        //get faskes
        $faskes=NULL;
        if($rujukan!=NULL){
            $r = new Referensi();
            $tingkat_faskes=1;
            $t=explode('!#!',$rujukan['asal_rujukan']['id']);
            if(!$r->faskes(1,$t[0])){
                $r->faskes(2,$t[0]);
            }
            $faskes=$r->data[0];
        }
        
        //poliklinik
        $poliklinik=NULL;
        if(!empty($data->poli)){
            $r = new Referensi();
            if($r->poliklinik($data->poli)){
                foreach($r->data as $rd){
                    if(trim($rd['nama'])==trim($data->poli)){
                        $poliklinik=$rd;
                        break;
                    }
                }
            }
        }

        //diagnosa
        $diagnosa=NULL;
        if($data->diagnosa!=NULL){
            $r = new Referensi();
            if($r->diagnosa($data->diagnosa)){
                foreach($r->data as $rd){
                    $x=explode(' - ',$rd['nama']);
                    if(trim($x[1])==trim($data->diagnosa)){
                        $diagnosa=$rd;
                    }
                }
            }
        }
        
        //hak kelas
        $kelas=array_values(array_filter(Params::KELAS,function($q)  use($data){
            $id=explode('!#!',$q['id']);
            return $id[0]==$data->klsRawat->klsRawatHak;
        }))[0];

        //naik kelas
        $status_naik_kelas=false;
        $kelas_naik=NULL;
        $kls_naik=$data->klsRawat->klsRawatNaik;
        if(!empty($kls_naik)){
            $status_naik_kelas=true;
            $kelas_naik=array_values(array_filter(Params::KELAS_NAIK,function($q)  use($kls_naik){
                $id=explode('!#!',$q['id']);
                return $id[0]==$kls_naik;
            }))[0];
        }
        
        $provinsi=NULL;
        $kabupaten=NULL;
        $kecamatan=NULL;
        if(!empty($data->lokasiKejadian->kdKec)){
            if($r->provinsi()){
                $prov=$r->data;
                $d=array_filter($prov,function($q) use($data){
                    $d=explode('!#!',$q['id']);
                    return $d[0]==$data->lokasiKejadian->kdProp;
                });
                if(count($d)>0){
                    $provinsi=array_values($d)[0];
                }
            }
            if($r->kabupaten($data->lokasiKejadian->kdProp)){
                $kab=$r->data;
                $d=array_filter($kab,function($q) use($data){
                    $d=explode('!#!',$q['id']);
                    return $d[0]==$data->lokasiKejadian->kdKab;
                });
                if(count($d)>0){
                    $kabupaten=array_values($d)[0];
                }
            }
            if($r->kecamatan($data->lokasiKejadian->kdKab)){
                $kab=$r->data;
                $d=array_filter($kab,function($q) use($data){
                    $d=explode('!#!',$q['id']);
                    return $d[0]==$data->lokasiKejadian->kdKec;
                });
                if(count($d)>0){
                    $kecamatan=array_values($d)[0];
                }
            }
        }
        
        //check if sep post rawatinap
        $post_ri=0;
        if(!empty($data->noRujukan)){
            if($this->searchSep($data->noRujukan)){
                if($this->data['jenis_layanan']==1){
                    $post_ri=1;
                }
            }
        }

        //get data dari table sep
        $db=NULL;
        if(!empty($data->noSep) && !empty($data->peserta->noKartu)){
            $db=DB::table($this->table)->where('sep_no_sep',$data->noSep)->where('sep_no_kartu',$data->peserta->noKartu)->whereNull('sep_deleted_at')->first();
        }
        
        $data=[
            'no_kartu'=>$data->peserta->noKartu,
            'nama'=>$data->peserta->nama,
            'tgl_lahir'=>$data->peserta->tglLahir!=NULL ? date('d-m-Y',strtotime($data->peserta->tglLahir)) : NULL,
            'jkel'=>$data->peserta->kelamin=='P' ? 'Perempuan' : 'Laki-laki',
            'no_sep'=>$data->noSep,
            'no_pasien'=>$data->peserta->noMr,
            'no_rujukan'=>$data->noRujukan,
            'tgl_sep'=>date('d-m-Y',strtotime($data->tglSep)),
            'tgl_rujukan'=>$rujukan!=NULL ? date('d-m-Y',strtotime($rujukan['tgl_rujukan'])) : NULL,
            'jenis_layanan'=>$data->jnsPelayanan=='Rawat Inap' ? 1 : 2,
            'jenis_peserta'=>$data->peserta->jnsPeserta,
            'kelas'=>$kelas,
            'kelas_rawat'=>$status_naik_kelas ? $kelas_naik : $kelas,
            'pembiayaan'=>$data->klsRawat->pembiayaan,
            'penanggung_jawab'=>$data->klsRawat->penanggungJawab,
            'asal_rujukan'=>$faskes,
            'diagnosa'=>$diagnosa,
            'poli_eksekutif'=>$data->poliEksekutif,
            'poliklinik'=>$poliklinik,
            'dpjp'=>$data->dpjp->kdDPJP!=0 || !empty($data->dpjp->kdDPJP) ? [
                'id'=>$data->dpjp->kdDPJP.'!#!'.$data->dpjp->nmDPJP,
                'nama'=>$data->dpjp->nmDPJP,
            ] : NULL,
            'cob'=>$data->cob,
            'katarak'=>$data->katarak,
            'post_ri'=>$post_ri,
            'catatan'=>$data->catatan,
            'keluhan'=>$rujukan!=NULL ? $rujukan['keluhan'] : '',
            'tujuan_kunjungan'=>$db!=NULL ? (
                $db->sep_tujuan_kunjungan!=NULL ? 
                    ['id'=>$db->sep_tujuan_kunjungan,'nama'=>Params::TUJUAN_KUNJUNGAN[$db->sep_tujuan_kunjungan]] : ''
                ) : '',
            'flag_prosedur'=>$db!=NULL ? (
                $db->sep_flag_prosedur!=NULL ? 
                    ['id'=>$db->sep_flag_prosedur,'nama'=>Params::FLAG_PROSEDUR[$db->sep_flag_prosedur]] : ''
                ) : '',
            'penunjang'=>$db!=NULL ? (
                $db->sep_penunjang_kode!=NULL ? 
                    ['id'=>$db->sep_penunjang_kode,'nama'=>Params::PENUNJANG[$db->sep_penunjang_kode]] : ''
                ) : '',
            'ases_pelayanan'=>$db!=NULL ? (
                $db->sep_ases_pelayanan!=NULL ? 
                    ['id'=>$db->sep_ases_pelayanan,'nama'=>Params::ASESMEN_PELAYANAN[$db->sep_ases_pelayanan]] : ''
                ) : '',
            'no_telp'=>$peserta!=NULL ? $peserta['no_telp'] : NULL,
            'laka_lantas'=>[
                'status'=>$data->kdStatusKecelakaan,
                'penjamin'=>$data->penjamin,
                'tgl_kejadian'=>$data->lokasiKejadian->tglKejadian!=NULL ? date('d-m-Y',strtotime($data->lokasiKejadian->tglKejadian)) : NULL,
                'ket'=>$data->lokasiKejadian->ketKejadian,
                'lokasi'=>$data->lokasiKejadian->lokasi,
                'provinsi'=>$provinsi,
                'kabupaten'=>$kabupaten,
                'kecamatan'=>$kecamatan
            ],
            'informasi'=>[
                'prb'=>$peserta!=NULL ? $peserta['informasi']['prolanis_prb'] : '',
            ],
            'no_surat_kontrol'=>$data->kontrol->noSurat,
        ];
        return $data;
    }
    function searchSep($sep)
    {
        if($this->vclaim->setup([
            'url'=>'SEP/'.trim($sep),
            'method'=>'GET'
        ])->run()){
            $this->data=$this->formatReturnSep($this->vclaim->getResponse());
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function validateSep($req)
    {
        //check format data
        $status=true;
        $var = ['ppk'=>'Pemberi Pelayanan Kesehatan','ppk_rujukan'=>'Pemberi pelayanan kesehatan rujukan','poliklinik'=>'Poliklinik','diagnosa'=>'Diagnosa','naik_kelas_rawat'=>'Naik Kelas Rawat','dpjp'=>'DPJP'];
        foreach($var as $k => $v){
            if(isset($req[$k]) && !empty($req[$k])){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg='Format '.$v.' tidak valid';
                    $status=false;
                    break;
                }
            }
        }
        
        //validate asesmen pelayanan
        if($req['jenis_layanan']==2){ //jika rawat jalan
            $beda_poli=false;
            if(!empty($req['no_rujukan'])){
                $rujukan=NULL;
                $p = new Rujukan();
                if(!$p->searchRujukan(1,1,$req['no_rujukan'])){
                    $p->searchRujukan(2,1,$req['no_rujukan']);
                }
                $rujukan=$p->data;
                if($rujukan!=NULL){
                    $t=explode('!#!',$rujukan['poliklinik']['id']);
                    $u=explode('!#!',$req['poliklinik']);
                    if($t[0]!=$u[0]){
                        $beda_poli=true;
                    }
                }
            }
            if(($req['tujuan_kunjungan']==0 && $beda_poli) || $req['tujuan_kunjungan']==2){
                if(!empty($req['asesmen_pelayanan'])){
                    if(!in_array($req['asesmen_pelayanan'],array_keys(Params::PENUNJANG))){
                        $this->error_msg='Data asesmen pelayanan tidak valid';
                        $status=false;
                    }
                }
            }
        }
        return $status;
    }
    function prepareData($req,$is_new=true)
    {
        $ppk_rujukan=explode('!#!',$req['ppk_rujukan']);
        $diagnosa=explode('!#!',$req['diagnosa']);

        $poliklinik=NULL;
        if($req['jenis_layanan']==2){
            $poliklinik=explode('!#!',$req['poliklinik']);
        }
        
        $dpjp=NULL;
        if(isset($req['dpjp']) && !empty($req['dpjp'])){
            $dpjp=explode('!#!',$req['dpjp']);
        }
        
        $kelas_rawat_naik=NULL;
        if(isset($req['naik_kelas_rawat'])){
            $kelas_rawat_naik=explode('!#!',$req['naik_kelas_rawat']);
        }
        
        $peserta['kelas']['id']=3;
        $peserta=NULL;
        $p = new Peserta();
        if($p->getData($req['no_kartu'],2)){
            $peserta=$p->data;
        }
        
        //setting hak kelas
        $kelas=NULL;
        if($peserta!=NULL){
            $t=explode('!#!',$peserta['kelas']['id']);
            $kelas=$t[0];
        }
        
        //set kelas for vclaim
        $kelas_rawat=[];
        $kelas_rawat['klsRawatHak']=$req['jenis_layanan']==2 ? 3 : ($kelas!=NULL ? $kelas : '');
        $kelas_rawat['klsRawatNaik']=$kelas_rawat_naik!=NULL ? $kelas_rawat_naik[0] : '';
        $kelas_rawat['pembiayaan']=$kelas_rawat_naik!=NULL ? $req['pembiayaan'] : '';
        $kelas_rawat['penanggungJawab']=$kelas_rawat_naik!=NULL ? ( $req['pembiayaan']==1 ? $req['penanggung_jawab'] : '' ) : '';
        
       $this->db_data=[
            'sep_no_kartu'=>$req['no_kartu'],
            'sep_pasien_kode'=>$req['no_pasien'],
            'sep_no_rujukan'=>!empty($req['no_rujukan']) ? $req['no_rujukan'] : '',
            'sep_tgl_sep'=>date('Y-m-d',strtotime(trim($req['tgl_sep']))),
            'sep_jenis_pelayanan'=>$req['jenis_layanan'],
            'sep_asal_rujukan_tingkat'=>$req['ppk_rujukan_tingkat'],
            'sep_asal_rujukan_kode'=>$ppk_rujukan[0],
            'sep_asal_rujukan_nama'=>$ppk_rujukan[1],
            'sep_tgl_rujukan'=>date('Y-m-d',strtotime(trim($req['tgl_rujukan']))),
            'sep_hak_kelas'=>$kelas,
            'sep_kelas_rawat'=>$req['jenis_layanan']==2 ? 3 : ( $kelas_rawat_naik!=NULL ? $kelas_rawat_naik[0] : $kelas),
            'sep_kelas_rawat_status'=>$kelas_rawat_naik!=NULL ? 1 : 0, 
            'sep_jenis_pembiayaan'=>isset($req['pembiayaan']) ? $req['pembiayaan'] : '',
            'sep_penanggung_jawab'=>isset($req['penanggung_jawab']) ? $req['penanggung_jawab'] : '',
            'sep_diagnosa_kode'=>$diagnosa[0],
            'sep_diagnosa_nama'=>$diagnosa[1],
            'sep_is_poli_eksekutif'=>$req['poliklinik_eksekutif'],
            'sep_is_cob'=>$req['cob'],
            'sep_is_katarak'=>$req['katarak'],
            'sep_tujuan_kunjungan'=>$req['tujuan_kunjungan'],
            'sep_flag_prosedur'=>!empty($req['flag_prosedur']) ? $req['flag_prosedur'] : '',
            'sep_penunjang_kode'=>!empty($req['penunjang']) ? $req['penunjang'] : '',
            'sep_simrs_penunjang_kode'=>!empty($req['penunjang']) ? (new MappingPenunjang())->getPenunjangSimrs($req['penunjang']) : '',
            'sep_ases_pelayanan'=>isset($req['asesmen_pelayanan']) ? $req['asesmen_pelayanan'] : '',
            'sep_skdp_no_surat'=>isset($req['asesmen_pelayanan']) ? ($req['asesmen_pelayanan']==5 ? $req['no_surat_kontrol'] : '') : '',
            'sep_is_laka_lantas'=>$req['laka_lantas'],
            'sep_no_telp'=>$req['no_telp'],
            'sep_catatan'=>$req['catatan'],
        ];
        
        if($poliklinik!=NULL){
            $this->db_data['sep_poli_kode']=$poliklinik[0];
            $this->db_data['sep_poli_nama']=$poliklinik[1];
            $this->db_data['sep_simrs_poli_kode']=(new MappingPoli())->getPoliSimrs($poliklinik[0]);
        }
        
        if($dpjp!=NULL){
            $this->db_data['sep_dpjp_kode']=$dpjp[0];
            $this->db_data['sep_dpjp_nama']=$dpjp[1];
            $this->db_data['sep_simrs_dpjp_kode']=(new MappingDpjp())->getDpjpSimrs($dpjp[0]);
        }

        if($is_new){
            $this->db_data['sep_created_at']=date('Y-m-d H:i:s');
            $this->db_data['sep_created_by']=$req['user'];
        }else{
            $this->db_data['sep_updated_at']=date('Y-m-d H:i:s');
            $this->db_data['sep_updated_by']=$req['user'];
        }
        
        $this->vclaim_data=[
            'request'=>[
                't_sep'=>[
                    'noKartu'=>trim($req['no_kartu']),
                    'tglSep'=>date('Y-m-d',strtotime(trim($req['tgl_sep']))),
                    'ppkPelayanan'=>env('BPJS_PPK_KODE'),
                    'jnsPelayanan'=>$req['jenis_layanan'],
                    'klsRawat'=>$kelas_rawat,
                    'noMR'=>(string) $req['no_pasien'],
                    'rujukan'=>[
                        'asalRujukan'=>$req['ppk_rujukan_tingkat'],
                        'tglRujukan'=>$req['tgl_rujukan']!=NULL ? date('Y-m-d',strtotime($req['tgl_rujukan'])) : '',
                        'noRujukan'=>$poliklinik!=NULL ? ($poliklinik[0]!='IGD' ? $req['no_rujukan'] : '') : $req['no_rujukan'] ,
                        'ppkRujukan'=>$ppk_rujukan[0],
                    ],
                    'catatan'=>$req['catatan'],
                    'diagAwal'=>$diagnosa[0],
                    'poli'=>[
                        'tujuan'=>$poliklinik!=NULL ? $poliklinik[0] : '',
                        'eksekutif'=>$req['poliklinik_eksekutif'],
                    ],
                    'cob'=>[
                        'cob'=>(string) $req['cob']
                    ],
                    'katarak'=>[
                        'katarak'=>(string) $req['katarak']
                    ],
                    'tujuanKunj'=>(string) $req['tujuan_kunjungan'],
                ]
            ]
        ];
        
        if(!$is_new){
            $this->vclaim_data['request']['t_sep']['noSep']=$req['no_sep'];
        }
        
        //jika kontrol 
        $surat_kontrol=NULL;
        if(isset($req['no_surat_kontrol'])){
            $k = new SuratKontrol();
            if($k->searchKontrol($req['no_surat_kontrol'])){
                $surat_kontrol=$k->data;
            }
        }

        $prov=NULL;
        $kab=NULL;
        $kec=NULL;
        if($req['laka_lantas']!=0){
            if(!empty($req['laka_lantas_prov'])){
                $prov=explode('!#!',$req['laka_lantas_prov']);
            }
            if(!empty($req['laka_lantas_kab'])){
                $kab=explode('!#!',$req['laka_lantas_kab']);
            }
            if(!empty($req['laka_lantas_kec'])){
                $kec=explode('!#!',$req['laka_lantas_kec']);
            }
        }
        
        $this->vclaim_data['request']['t_sep']['flagProcedure']=(string) isset($req['flag_prosedur']) ? $req['flag_prosedur'] : '';
        $this->vclaim_data['request']['t_sep']['kdPenunjang']=!empty($req['penunjang']) ? $req['penunjang'] : '';
        $this->vclaim_data['request']['t_sep']['assesmentPel']=isset($req['asesmen_pelayanan']) ? $req['asesmen_pelayanan'] : '';
        $this->vclaim_data['request']['t_sep']['skdp']['noSurat']=isset($req['no_surat_kontrol']) ? $req['no_surat_kontrol'] : '';
        $this->vclaim_data['request']['t_sep']['skdp']['kodeDPJP']=$surat_kontrol!=NULL ? explode('!#!',$surat_kontrol['dokter']['id'])[0] : '';
        $this->vclaim_data['request']['t_sep']['dpjpLayan']=$req['jenis_layanan']==2 ? ($dpjp!=NULL ? $dpjp[0] : '') : '';
        $this->vclaim_data['request']['t_sep']['noTelp']=$req['no_telp'];
        $this->vclaim_data['request']['t_sep']['user']=setUser($req['user']);
        //laka lantas bridging
        $this->vclaim_data['request']['t_sep']['jaminan']['lakaLantas']=(string) $req['laka_lantas'];
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['tglKejadian']=$req['laka_lantas']!=0 ? ( $req['laka_lantas_tgl_kejadian']!=NULL ? date('Y-m-d',strtotime($req['laka_lantas_tgl_kejadian'])) : '' ) : '';
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['keterangan']=$req['laka_lantas']!=0 ? $req['laka_lantas_ket'] : '';
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['suplesi']['suplesi']=$req['laka_lantas']!=0 ? $req['laka_lantas_suplesi'] : 0;
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['suplesi']['noSepSuplesi']=$req['laka_lantas']!=0 ? ( $req['laka_lantas_suplesi']==1 ? (string) $req['laka_lantas_no_suplesi'] : '' ) : '';
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdPropinsi']=$prov!=NULL ? $prov[0] : '';
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdKabupaten']=$kab!=NULL ? $kab[0] : '';
        $this->vclaim_data['request']['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdKecamatan']=$kec!=NULL ? $kec[0] : '';
        
        if($req['laka_lantas']!=0){
            $this->db_data['sep_laka_lantas_tgl_kejadian']=$req['laka_lantas_tgl_kejadian']!=NULL ? date('Y-m-d',strtotime($req['laka_lantas_tgl_kejadian'])) : '';
            $this->db_data['sep_laka_lantas_ket']=$req['laka_lantas_ket'];
            $this->db_data['sep_laka_lantas_suplesi']=$req['laka_lantas_suplesi'];
            if($prov!=NULL){
                $this->db_data['sep_laka_lantas_prov_kode']=$prov[0];
            }
            if($kab!=NULL){
                $this->db_data['sep_laka_lantas_kab_kode']=$kab[0];
            }
            if($kec!=NULL){
                $kec=explode('!#!',$req['laka_lantas_kec']);
                $this->db_data['sep_laka_lantas_kec_kode']=$kec[0];
            }
            if($req['laka_lantas_suplesi']==1){
                $this->db_data['sep_laka_lantas_no_suplesi']=$req['laka_lantas_no_suplesi'];
            }
        }
    }
    function saveSep($req)
    {
        if($this->validateSep($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try{
                    $insert = DB::table($this->table)->insertGetId($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'SEP/2.0/insert',
                        'method'=>'POST',
                        'param'=>$this->vclaim_data,
                    ])->run()){
                        $data=$this->vclaim->getResponse();
                        $update_sep=DB::table($this->table)->where('sep_id','=',$insert)->update(['sep_no_sep'=>$data->sep->noSep]);
                        $this->data=[
                            'id'=>$insert,
                            'no_sep'=>$data->sep->noSep
                        ];
                        DB::commit();
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::rollBack();
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function updateSep($req)
    {
        if($this->validateSep($req)){
            $this->prepareData($req,false);
            DB::beginTransaction();
                try{
                    $update = DB::table($this->table)->where('sep_no_sep','=',$req['no_sep'])->where('sep_pasien_kode','=',$req['no_pasien'])->whereNull('sep_deleted_at')->update($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'SEP/2.0/update',
                        'method'=>'PUT',
                        'param'=>$this->vclaim_data,
                    ])->run()){
                        $data=$this->vclaim->getResponse();
                        $this->data=[
                            'no_sep'=>$data->sep->noSep,
                        ];
                        DB::commit();
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::rollBack();
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function deleteSep($req)
    {
        DB::beginTransaction();
            try{
                $update = DB::table($this->table)->where('sep_no_sep','=',$req['no_sep'])->whereNull('sep_deleted_at')->update(['sep_deleted_at'=>date('Y-m-d H:i:s'),'sep_deleted_by'=>$req['user']]);
                if($this->vclaim->setup([
                    'url'=>'SEP/2.0/delete',
                    'method'=>'DELETE',
                    'param'=>[
                        'request'=>[
                            't_sep'=>[
                                'noSep'=>$req['no_sep'],
                                'user'=>setUser($req['user']),
                            ]
                        ]
                    ],
                ])->run()){
                    DB::commit();
                    return true;
                }else{
                    $this->error_msg=$this->vclaim->error_msg;
                    DB::rollBack();
                }
            }catch(\Illuminate\Database\QueryException $ex){ 
                $this->error_msg=$ex->getMessage();
                DB::rollBack();
            }
        return false;
    }
    function updateSepPulang($req)
    {
        DB::beginTransaction();
            try{
                $update = DB::table($this->table)->where('sep_no_sep','=',$req['no_sep'])->where('sep_pasien_kode','=',$req['no_pasien'])->whereNull('sep_tgl_checkout_sep')->whereNull('sep_deleted_at')->update(['sep_tgl_checkout_sep'=>date('Y-m-d',strtotime($req['tgl_pulang'])),'sep_updated_at'=>date('Y-m-d H:i:s'),'sep_updated_by'=>$req['user']]);
                if($this->vclaim->setup([
                    'url'=>'SEP/2.0/updtglplg',
                    'method'=>'PUT',
                    'param'=>[
                        'request'=>[
                            't_sep'=>[
                                'noSep'=>$req['no_sep'],
                                'statusPulang'=>$req['status_pulang'],
                                'noSuratMeninggal'=>isset($req['no_surat_meninggal']) ? $req['no_surat_meninggal'] : '',
                                'tglMeninggal'=>isset($req['tgl_meninggal']) ? date('Y-m-d',strtotime($req['tgl_meninggal'])) : '',
                                'tglPulang'=>date('Y-m-d',strtotime($req['tgl_pulang'])),
                                'noLPManual'=>isset($req['no_lp_manual']) ? $req['no_lp_manual'] : '',
                                'user'=>setUser($req['user']),
                            ]
                        ]
                    ],
                ])->run()){
                    DB::commit();
                    return true;
                }else{
                    $this->error_msg=$this->vclaim->error_msg;
                    DB::rollBack();
                }
            }catch(\Illuminate\Database\QueryException $ex){ 
                $this->error_msg=$ex->getMessage();
                DB::rollBack();
            }
        return false;
    }
    function listSepInternal($no_sep)
    {
        if($this->vclaim->setup([
            'url'=>'SEP/Internal/'.trim($no_sep),
            'method'=>'GET',
        ])->run()){
            $d=$this->vclaim->getResponse();
            foreach($d->list as $q){

                $poli_asal=NULL;
                $r = new Referensi();
                if($r->poliklinik($q->nmpoliasal)){
                    $poli_asal=$r->matchByName($q->nmpoliasal);
                }

                $ppk_pelayanan=NULL;
                $r = new Referensi();
                if(!$r->faskes(1,$q->ppkpelsep)){
                    $r->faskes(2,$q->ppkpelsep);
                }
                $ppk_pelayanan=$r->data[0];

                $penunjang=NULL;
                if($q->kdpenunjang!=0){
                    $penunjang=[
                        'id'=>$q->kdpenunjang!=0 ? $q->kdpenunjang.'!#!'.Params::PENUNJANG[$q->kdpenunjang] : NULL,
                        'nama'=>Params::PENUNJANG[$q->kdpenunjang]
                    ];
                }

                $diagnosa=NULL;
                $r = new Referensi();
                if($r->diagnosa($q->nmdiag)){
                    $diagnosa=$r->matchByName($q->nmdiag);
                }

                $this->data[]=[
                    'no_kartu'=>$q->nokapst,
                    'no_sep'=>$q->nosep,
                    'no_sep_referensi'=>$q->nosepref,
                    'tgl_sep'=>$q->tglsep!=NULL ? date('d-m-Y',strtotime($q->tglsep)) : NULL,
                    'tgl_rujukan_internal'=>$q->tglrujukinternal!=NULL ? date('d-m-Y',strtotime($q->tglrujukinternal)) : NULL,
                    'no_surat'=>$q->nosurat,
                    'ppk_pelayanan'=>$ppk_pelayanan,
                    'poli_tujuan'=>[
                        'id'=>$q->tujuanrujuk.'!#!'.$q->nmtujuanrujuk,
                        'nama'=>$q->nmtujuanrujuk,
                    ],
                    'poli_asal'=>$poli_asal,
                    'flag_internal'=>$q->flaginternal,
                    'flag_prosedur'=>$q->flagprosedur,
                    'flag_sep'=>$q->flagsep,
                    'penunjang'=>$penunjang,
                    'dokter'=>[
                        'id'=>$q->kddokter.'!#!'.$q->nmdokter,
                        'nama'=>$q->nmdokter,
                    ],
                    'diagnosa'=>$diagnosa,
                    'konsul'=>$q->opsikonsul,
                    'user'=>$q->fuser,
                    'tanggal'=>$q->fdate!=NULL ? date('d-m-Y',strtotime($q->fdate)) : NULL,
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
        return false;
    }
    function deleteSepInternal($req)
    {
        $poli=explode('!#!',$req['poliklinik']);
        if($this->vclaim->setup([
            'url'=>'SEP/Internal/delete',
            'method'=>'DELETE',
            'param'=>[
                'request'=>[
                    't_sep'=>[
                        'noSep'=>$req['no_sep'],
                        'noSurat'=>$req['no_surat'],
                        'tglRujukanInternal'=>isset($req['tgl_rujukan']) ? date('Y-m-d',strtotime($req['tgl_rujukan'])) : '',
                        'kdPoliTuj'=>$poli[0],
                        'user'=>setUser($req['user']),
                    ]
                ]
            ],
        ])->run()){
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
        }
    }
    function searchPostRawatinap($req)
    {
        $no_kartu=$req['no_kartu'];
        
        $tgl_awal=date('Y-m-d',strtotime('-3 months'));
        if(isset($req['tgl_awal'])){
            $tgl_awal=date('Y-m-d',strtotime($req['tgl_awal']));
        }
        $tgl_akhir=date('Y-m-d');
        if($tgl_akhir==NULL){
            $tgl_akhir=date('Y-m-d',strtotime($req['tgl_akhir']));
        }
        $m = new Monitoring();
        if($m->historipelayanan($no_kartu,$tgl_awal,$tgl_akhir)){
            $result=$m->data;
            
            //sorting desc berdasarkan tgl sep
            usort($result,function($q1,$q2){
                $time1 = strtotime($q1['tgl_sep']);
                $time2 = strtotime($q2['tgl_sep']);
                return $time2-$time1;
            });
            
            $rawatinap=[];
            $no_sep=NULL;
            foreach($result as $r){
                if($r['ppk_pelayanan']!=NULL){
                    $ppk_kode=explode('!#!',$r['ppk_pelayanan']['id']);
                    if($r['jenis_layanan']['id']==1 && $ppk_kode[0]==env('BPJS_PPK_KODE')){
                        $no_sep=$r['no_sep'];

                        //cek apakah sudah kontrol
                        $post_kontrol_ri=array_filter($result,function($e) use($r){
                            return $e['no_rujukan']==$r['no_sep'];
                        });

                        if(count($post_kontrol_ri)<1){
                            $rawatinap[]=$r;
                        }
                    }
                }
            }
            
            if(count($rawatinap)>0){
                foreach($rawatinap as $q){
                    $this->data[]=[
                        'nama'=>$q['nama'],
                        'no_pasien'=>$q['no_pasien'],
                        'no_kartu'=>$q['no_kartu'],
                        'no_rujukan'=>!empty($q['tgl_sep_plg']) ? $q['no_sep'] : 'PASIEN BELUM PULANG',
                        'tgl_rujukan'=>$q['tgl_sep'],
                        'kelas'=>$q['kelas'],
                        'diagnosa'=>$q['diagnosa'],
                        'ppk_pelayanan'=>$q['ppk_pelayanan'],
                        'no_telp'=>$q['no_telp'],
                        'tgl_sep_plg'=>$q['tgl_sep_plg'],
                    ];
                }
                return true;
            }else{
                $this->error_msg="Tidak ditemukan SEP Rawat Inap";
            }
        }else{
            $this->error_msg=$m->error_msg;
        }
        return false;
    }
    function searchSepIgd($no_kartu)
    {
        $m = new Monitoring();
        if($m->historipelayanan($no_kartu,date('Y-m-d',strtotime('-2 days')),date('Y-m-d'))){
            $result=$m->data;

            usort($result,function($q1,$q2){
                $time1 = strtotime($q1['tgl_sep']);
                $time2 = strtotime($q2['tgl_sep']);
                return $time2-$time1;
            });
            
            $find=NULL;
            foreach($result as $d){
                $ppk_kode=explode('!#!',$d['poliklinik']['id']);
                if($ppk_kode[0]=='IGD'){
                    $find=$d;
                    break;
                }
            }

            if($find!=NULL){
                $this->data=[
                    'nama'=>$find['nama'],
                    'no_kartu'=>$find['no_kartu'],
                    'no_rujukan'=>$find['no_sep'],
                ];
                $this->error_msg="SEP IGD ditemukan. An. ".$find['nama'].', Tgl Terbit : '.$find['tgl_sep'];
                return true;
            }
            $this->error_msg="SEP IGD tidak ditemukan ";
        }else{
            $this->error_msg=$m->error_msg;
        }
        return false;
    }
}