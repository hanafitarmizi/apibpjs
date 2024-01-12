<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class SuratKontrol extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_surat_kontrol';
    protected $primaryKey = 'sk_id';
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function validateData($req)
    {
        $var = ['dokter'=>'Dokter','poliklinik'=>'Poliklinik'];
        if(count($var)>0){
            foreach($var as $k => $v){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg='Format '.$v.' tidak valid';
                    return false;
                }
            }
        }
        return true;
    }
    function prepareData($req,$is_new=true)
    {
        $dokter=explode('!#!',$req['dokter']);
        $poliklinik=explode('!#!',$req['poliklinik']);
        $this->vclaim_data=[
            'request'=>[
                'noSEP'=>$req['no_sep'],
                'kodeDokter'=>$dokter[0],
                'poliKontrol'=>$poliklinik[0],
                'tglRencanaKontrol'=>date('Y-m-d',strtotime($req['tgl_kontrol'])),
                'user'=>setUser($req['user'])
            ]
        ];
        $this->db_data=[
            'sk_pasien_kode'=>$req['no_pasien'],
            'sk_reg_kode'=>$req['no_daftar'],
            'sk_no_sep'=>$req['no_sep'],
            'sk_tgl_kontrol'=>date('Y-m-d',strtotime($req['tgl_kontrol'])),
            'sk_bpjs_unit_kode'=>$poliklinik[0],
            'sk_bpjs_unit_nama'=>$poliklinik[1],
            'sk_simrs_unit_kode'=>(new MappingPoli())->getPoliSimrs($poliklinik[0]), //unit simrs
            'sk_bpjs_dokter_kode'=>$dokter[0],
            'sk_bpjs_dokter_nama'=>$dokter[1],
            'sk_ket'=>isset($req['ket']) ? $req['ket'] : NULL,
        ];
        if($is_new){
            $this->db_data['sk_created_by']=$req['user'];
            $this->db_data['sk_created_at']=date('Y-m-d H:i:s');
        }else{
            $this->vclaim_data['request']['noSuratKontrol']=$req['no_surat_kontrol'];
            $this->db_data['sk_updated_by']=$req['user'];
            $this->db_data['sk_updated_at']=date('Y-m-d H:i:s');
        }
    }
    function insertKontrol($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::connection($this->connection)->beginTransaction();
                try {
                    $insert=DB::connection($this->connection)->table($this->table)->insertGetId($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'RencanaKontrol/insert',
                        'method'=>"POST",
                        'param'=>$this->vclaim_data
                    ])->run()){
                        $result=$this->vclaim->getResponse();
                        DB::connection($this->connection)->table($this->table)->where('sk_id',$insert)->update(['sk_no_surat'=>$result->noSuratKontrol]);
                        DB::connection($this->connection)->commit();
                        $this->data=['id'=>$insert,'no_surat_kontrol'=>$result->noSuratKontrol];
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::connection($this->connection)->rollBack();
                    }
                } catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::connection($this->connection)->rollBack();
                }
        }
        return false;
    }
    function updateKontrol($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req,false);
            DB::beginTransaction();
                try {
                    $update=DB::connection($this->connection)->table($this->table)->where('sk_id','=',$req['id'])->update($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'RencanaKontrol/Update',
                        'method'=>"PUT",
                        'param'=>$this->vclaim_data
                    ])->run()){
                        $result=$this->vclaim->getResponse();
                        $this->data=['id'=>$req['id'],'no_surat_kontrol'=>$result->noSuratKontrol];
                        DB::commit();
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::rollBack();
                    }
                } catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function deleteKontrol($id,$no_surat,$user)
    {
        DB::beginTransaction();
            try {
                $delete=DB::connection($this->connection)->table($this->table)->where('sk_id','=',$id)->update(['sk_deleted_at'=>date('Y-m-d H:i:s'),'sk_deleted_by'=>$user]);
                if($this->vclaim->setup([
                    'url'=>'RencanaKontrol/Delete',
                    'method'=>"DELETE",
                    'param'=>[
                        'request'=>[
                            't_suratkontrol'=>[
                                'noSuratKontrol'=>$no_surat,
                                'user'=>setUser($user),
                            ]
                        ]
                    ]
                ])->run()){
                    DB::commit();
                    return true;
                }else{
                    $this->error_msg=$this->vclaim->error_msg;
                    DB::rollBack();
                }
            } catch(\Illuminate\Database\QueryException $ex){ 
                $this->error_msg=$ex->getMessage();
                DB::rollBack();
            }
        return false;
    }
    function formatReturnKontrol($q)
    {
        $poliklinik=NULL;
        if($q->poli!=NULL){
            $t=explode('-',$q->poli);
            $p = new Referensi();
            if($p->poliklinik(trim($t[0]))){
                $poliklinik=$p->data;
            }
        }

        $diagnosa=NULL;
        if($q->diagnosa!=NULL){
            $t=explode('-',$q->diagnosa);
            $p = new Referensi();
            if($p->diagnosa(trim($t[0]))){
                $diagnosa=$p->data;
            }
        }
        $tmp=[
            'no_kartu'=>$q->peserta->noKartu,
            'nama'=>$q->peserta->nama,
            'tgl_lahir'=>$q->tglLahir!=NULL ? date('d-m-Y',strtotime($q->tglLahir)) : NULL,
            'jkel'=>$q->kelamin,
            'hak_kelas'=>$q->hakKelas,
            'no_sep'=>$q->noSep,
            'tgl_sep'=>$q->tglSep!=NULL ? date('d-m-Y',strtotime($q->tglSep)) : NULL,
            'dokter'=>[
                'id'=>$q->kodeDokter."!#!".$q->namaDokter,
                'nama'=>$q->namaDokter,
            ],
            'jenis_layanan'=>[
                'id'=>$q->jnsPelayanan=='Rawat Jalan' ? 2 : 1,
                'nama'=>$q->jnsPelayanan,
            ],
            'poliklinik'=>$poliklinik,
            'diagnosa'=>$diagnosa,
            'rujukan_asal'=>[
                'id'=>$q->provUmum->kdProvider,
                'nama'=>$q->provUmum->nmProvider,
            ],
            'perujuk'=>[
                'id'=>$q->provPerujuk->kdProviderPerujuk,
                'nama'=>$q->provPerujuk->nmProviderPerujuk,
                'tingkat'=>$q->provPerujuk->asalRujukan,
                'no_rujukan'=>$q->provPerujuk->noRujukan,
                'tgl_rujukan'=>$q->provPerujuk->tglRujukan!=NULL ? date('d-m-Y',strtotime($q->provPerujuk->tglRujukan)) : NULL,
            ]
        ];
        return $tmp;
    }
    function searchSepKontrol($no_sep)
    {
        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/nosep/'.trim($no_sep),
            'method'=>'GET'
        ])->run()){
            $this->data=$this->formatReturnKontrol($this->vclaim->getResponse());
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function searchKontrol($nomor)
    {
        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/noSuratKontrol/'.trim($nomor),
            'method'=>'GET'
        ])->run()){
            $q=$this->vclaim->getResponse();
            $this->data=[
                    'nama'=>$q->sep->peserta->nama,
                    'jkel'=>$q->sep->peserta->kelamin=='L' ? 'Laki-laki' : 'Perempuan',
                    'tgl_lahir'=>$q->sep->peserta->tglLahir!=NULL ? date('d-m-Y',strtotime($q->sep->peserta->tglLahir)) : '',
                    'no_kartu'=>$q->sep->peserta->noKartu,
                    'no_sep'=>$q->sep->noSep,
                    'no_surat_kontrol'=>$q->noSuratKontrol,
                    'tgl_kontrol'=>$q->tglRencanaKontrol!=NULL ? date('d-m-Y',strtotime($q->tglRencanaKontrol)) : '',
                    'tgl_terbit'=>$q->tglTerbit!=NULL ? date('d-m-Y',strtotime($q->tglTerbit)) : '',
                    'jenis'=>$q->jnsKontrol==1 ? 'SPRI' : 'Surat Kontrol',
                    'poliklinik'=>[
                        'id'=>$q->poliTujuan.'!#!'.$q->namaPoliTujuan,
                        'nama'=>$q->namaPoliTujuan
                    ],
                    'dokter'=>[
                        'id'=>$q->kodeDokter."!#!".$q->namaDokter,
                        'nama'=>$q->namaDokter,
                    ],
                    'diagnosa'=>[
                        'id'=>!empty($q->sep->diagnosa) ? explode(' - ',$q->sep->diagnosa)[0].'!#!'.$q->sep->diagnosa : '',
                        'nama'=>$q->sep->diagnosa,
                    ]
                ];
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function getListKontrolByKartu($bln,$thn,$nomor,$jenis)
    {
        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/ListRencanaKontrol/Bulan/'.trim($bln).'/Tahun/'.trim($thn).'/Nokartu/'.trim($nomor).'/filter/'.trim($jenis),
            'method'=>'GET'
        ])->run()){
            $tmp=$this->vclaim->getResponse();
            foreach($tmp->list as $q){
                $this->data[]=[
                    'nama'=>$q->nama,
                    'no_kartu'=>$q->noKartu,
                    'no_surat_kontrol'=>$q->noSuratKontrol,
                    'no_sep_asal'=>$q->noSepAsalKontrol,
                    'tgl_sep'=>$q->tglSEP!=NULL ? date('d-m-Y',strtotime($q->tglSEP)) : NULL,
                    'jenis_layanan_kode'=>$q->jnsPelayanan=='Rawat Jalan' ? 2 : 1,
                    'jenis_layanan'=>[
                        'id'=>$q->jnsPelayanan=='Rawat Jalan' ? 2 : 1,
                        'nama'=>$q->jnsPelayanan,
                    ],
                    'jenis'=>[
                        'id'=>$q->jnsKontrol,
                        'nama'=>$q->namaJnsKontrol,
                    ],
                    'tgl_kontrol'=>$q->tglRencanaKontrol!=NULL ? date('d-m-Y',strtotime($q->tglRencanaKontrol)) : NULL,
                    'tgl_terbit_kontrol'=>$q->tglTerbitKontrol!=NULL ? date('d-m-Y',strtotime($q->tglTerbitKontrol)) : NULL,
                    'poliklinik_asal'=>[
                        'id'=>$q->poliAsal.'!#!'.$q->namaPoliAsal,
                        'nama'=>$q->namaPoliAsal,
                    ],
                    'poliklinik_tujuan'=>[
                        'id'=>$q->poliTujuan.'!#!'.$q->namaPoliTujuan,
                        'nama'=>$q->namaPoliTujuan,
                    ],
                    'dokter'=>[
                        'id'=>$q->kodeDokter.'!#!'.$q->namaDokter,
                        'nama'=>$q->namaDokter,
                    ]
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function getListKontrol($jenis,$tgl_awal,$tgl_akhir)
    {
        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/ListRencanaKontrol/tglAwal/'.date('Y-m-d',strtotime(trim($tgl_awal))).'/tglAkhir/'.date('Y-m-d',strtotime(trim($tgl_akhir))).'/filter/'.trim($jenis),
            'method'=>'GET'
        ])->run()){
            $tmp=$this->vclaim->getResponse();
            foreach($tmp->list as $q){
                $this->data[]=[
                    'nama'=>$q->nama,
                    'no_kartu'=>$q->noKartu,
                    'no_surat_kontrol'=>$q->noSuratKontrol,
                    'no_sep_asal'=>$q->noSepAsalKontrol,
                    'tgl_sep'=>$q->tglSEP!=NULL ? date('d-m-Y',strtotime($q->tglSEP)) : NULL,
                    'jenis_layanan_kode'=>$q->jnsPelayanan=='Rawat Jalan' ? 2 : 1,
                    'jenis_layanan'=>[
                        'id'=>$q->jnsPelayanan=='Rawat Jalan' ? 2 : 1,
                        'nama'=>$q->jnsPelayanan,
                    ],
                    'jenis'=>[
                        'id'=>$q->jnsKontrol,
                        'nama'=>$q->namaJnsKontrol,
                    ],
                    'tgl_kontrol'=>$q->tglRencanaKontrol!=NULL ? date('d-m-Y',strtotime($q->tglRencanaKontrol)) : NULL,
                    'tgl_terbit_kontrol'=>$q->tglTerbitKontrol!=NULL ? date('d-m-Y',strtotime($q->tglTerbitKontrol)) : NULL,
                    'poliklinik_asal'=>[
                        'id'=>$q->poliAsal.'!#!'.$q->namaPoliAsal,
                        'nama'=>$q->namaPoliAsal,
                    ],
                    'poliklinik_tujuan'=>[
                        'id'=>$q->poliTujuan.'!#!'.$q->namaPoliTujuan,
                        'nama'=>$q->namaPoliTujuan,
                    ],
                    'dokter'=>[
                        'id'=>$q->kodeDokter.'!#!'.$q->namaDokter,
                        'nama'=>$q->namaDokter,
                    ]
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function getListPoliklinik($jenis,$nomor,$tgl_kontrol)
    {
        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/ListSpesialistik/JnsKontrol/'.trim($jenis).'/nomor/'.trim($nomor).'/TglRencanaKontrol/'.date('Y-m-d',strtotime(trim($tgl_kontrol))),
            'method'=>'GET'
        ])->run()){
            $t=$this->vclaim->getResponse();
            foreach($t->list as $q){
                $this->data[]=[
                    'poliklinik'=>[
                        'id'=>$q->kodePoli.'!#!'.$q->namaPoli,
                        'nama'=>$q->namaPoli,
                    ],
                    'kapasitas'=>$q->kapasitas,
                    'jml_kontrol_rujukan'=>$q->jmlRencanaKontroldanRujukan,
                    'persentase'=>$q->persentase,
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function getListDokter($jenis,$poliklinik,$tgl_kontrol)
    {
        $poliklinik=explode('!#!',$poliklinik);
        if(count($poliklinik)<2){
            $this->error_msg="Format poliklinik tidak valid";
            return false;
        }

        if($this->vclaim->setup([
            'url'=>'RencanaKontrol/JadwalPraktekDokter/JnsKontrol/'.trim($jenis).'/KdPoli/'.trim($poliklinik[0]).'/TglRencanaKontrol/'.date('Y-m-d',strtotime(trim($tgl_kontrol))),
            'method'=>'GET'
        ])->run()){
            $t=$this->vclaim->getResponse();
            foreach($t->list as $q){
                $this->data[]=[
                    'dokter'=>[
                        'id'=>$q->kodeDokter.'!#!'.$q->namaDokter,
                        'nama'=>$q->namaDokter,
                    ],
                    'jadwal_praktek'=>$q->jadwalPraktek,
                    'kapasitas'=>$q->kapasitas
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
}