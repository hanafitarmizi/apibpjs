<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PengajuanKlaim extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'bpjskes.lembar_pengajuan_klaim';
    protected $primaryKey = 'id';
    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;
    
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function prepareData($req,$is_new=true)
    {
        $jaminan=explode('!#!',$req['jaminan']);
        $poli=explode('!#!',$req['poli']);
        $ruang_rawat=explode('!#!',$req['ruang_rawat']);
        $kelas_rawat=explode('!#!',$req['kelas_rawat']);
        $spesialis=explode('!#!',$req['spesialis']);
        $cara_keluar=explode('!#!',$req['cara_keluar']);
        $kondisi_pulang=explode('!#!',$req['kondisi_pulang']);
        $spesialis=explode('!#!',$req['spesialis']);
        $ppk=explode('!#!',$req['ppk']);
        $poli_kontrol=explode('!#!',$req['poli_kontrol']);
        $dpjp=explode('!#!',$req['dpjp']);

        //format diagnosa for bridging
        $diag=NULL;
        if(isset($req['diagnosa'])){
            foreach($req['diagnosa'] as $q){
                $exp=explode('!#!',$q['id']);
                $diag[]=['id'=>$exp[0],'level'=>$q['level']];
            }
        }

        //format prosedur for bridging
        $proc=NULL;
        if(isset($req['prosedur'])){
            foreach($req['prosedur'] as $q){
                $exp=explode('!#!',$q['id']);
                $proc[]=['id'=>$exp[0]];
            }
        }

        $this->vclaim_data=[
            'request'=>[
                't_lpk'=>[
                    'noSep'=>$req['no_sep'],
                    'tglMasuk'=>$req['tgl_masuk']!=NULL ? date('Y-m-d',strtotime($req['tgl_masuk'])) : NULL,
                    'tglKeluar'=>$req['tgl_keluar']!=NULL ? date('Y-m-d',strtotime($req['tgl_keluar'])) : NULL,
                    'jaminan'=>$jaminan[0],
                    'poli'=>[
                        'poli'=>$poli[0]
                    ],
                    'perawatan'=>[
                        "ruangRawat"=>$ruang_rawat[0],
                        "kelasRawat"=>$kelas_rawat[0],
                        "spesialistik"=>$spesialis[0],
                        "caraKeluar"=>$cara_keluar[0],
                        "kondisiPulang"=>$kondisi_pulang[0]
                    ],
                    "rencanaTL"=>[
                        "tindakLanjut"=>$req['tindak_lanjut'],
                        "dirujukKe"=>[
                           "kodePPK"=>$ppk[0]
                        ],
                        "kontrolKembali"=>[
                           "tglKontrol"=>$req['tgl_kontrol']!=NULL ? date('Y-m-d',strtotime($req['tgl_kontrol'])) : NULL,
                           "poli"=>$poli_kontrol[0]
                        ]
                    ],
                    "DPJP"=>$dpjp[0],
                    "user"=>setUser($req['user'])
                ]
            ]
        ];
        $this->vclaim_data['request']['t_lpk']['diagnosa']=$diag;
        $this->vclaim_data['request']['t_lpk']['procedure']=$proc;

        $this->db_data=[
            'no_sep'=>$req['no_sep'],
            'no_pasien'=>$req['no_pasien'],
            'no_daftar'=>$req['no_daftar'],
            'tgl_masuk'=>date('Y-m-d',strtotime($req['tgl_masuk'])),
            'tgl_keluar'=>date('Y-m-d',strtotime($req['tgl_keluar'])),
            'jaminan_kode'=>$jaminan[0],
            'jaminan_nama'=>$jaminan[1],
            'bpjs_unit_kode'=>$poli[0],
            'bpjs_unit_nama'=>$poli[1],
            'simrs_unit_kode'=>(new MappingPoli())->getPoliSimrs($poli[0]),
            'bpjs_ruang_rawat_kode'=>$ruang_rawat[0],
            'bpjs_ruang_rawat_nama'=>$ruang_rawat[1],
            'simrs_ruang_rawat_kode'=>(new MappingRuang())->getRuangSimrs($ruang_rawat[0]),
            'bpjs_kelas_rawat_kode'=>$kelas_rawat[0],
            'bpjs_kelas_rawat_nama'=>$kelas_rawat[1],
            'simrs_kelas_rawat_kode'=>(new MappingKelasRawat())->getKelasRawatSimrs($kelas_rawat[0]),
            'bpjs_spesialis_kode'=>$spesialis[0],
            'bpjs_spesialis_nama'=>$spesialis[1],
            'bpjs_cara_keluar_kode'=>$cara_keluar[0],
            'bpjs_cara_keluar_nama'=>$cara_keluar[1],
            'simrs_cara_keluar_kode'=>(new MappingCaraKeluar())->getCaraKeluarSimrs($cara_keluar[0]),
            'bpjs_kondisi_pulang_kode'=>$kondisi_pulang[0],
            'bpjs_kondisi_pulang_nama'=>$kondisi_pulang[1],
            'simrs_kondisi_pulang_kode'=>(new MappingKondisiPulang())->getKondisiPulangSimrs($cara_keluar[0]),
            'tindak_lanjut'=>$req['tindak_lanjut'],
            'bpjs_ppk_kode'=>$ppk[0],
            'bpjs_ppk_nama'=>$ppk[1],
            'tgl_kontrol'=>date('Y-m-d',strtotime($req['tgl_kontrol'])),
            'bpjs_unit_kontrol_kode'=>$poli_kontrol[0],
            'bpjs_unit_kontrol_nama'=>$poli_kontrol[1],
            'bpjs_dpjp_kode'=>$dpjp[0],
            'bpjs_dpjp_nama'=>$dpjp[1],
            'simrs_dpjp_kode'=>(new MappingDpjp())->getDpjpSimrs($dpjp[0]),
        ];
        if($is_new){
            $this->db_data['created_at']=date('Y-m-d H:i:s');
            $this->db_data['created_by']=$req['user'];
        }else{
            $this->db_data['updated_at']=date('Y-m-d H:i:s');
            $this->db_data['updated_by']=$req['user'];
        }
    }
    function getData($tgl_masuk,$jenis_layanan)
    {
        if($this->vclaim->setup([
            'method'=>'GET',
            'url'=>'LPK/TglMasuk/'.date('Y-m-d',strtotime($tgl_masuk)).'/JnsPelayanan/'.$jenis_layanan
        ])->run()){
            $r=$this->vclaim->getResponse();
            foreach($r->list as $q){

                $diagnosa=NULL;
                foreach($q->diagnosa->list as $q){
                    $diagnosa[]=[
                        'id'=>$q->list->kode.'!#!'.$q->list->nama,
                        'nama'=>$q->list->nama,
                        'level'=>$q->level,
                    ];
                }

                $this->data[]=[
                    'dpjp'=>[
                        'id'=>$q->DPJP->dokter->kode.'!#!'.$q->DPJP->dokter->nama,
                        'nama'=>$q->DPJP->dokter->nama,
                    ],
                    'diagnosa'=>$diagnosa,
                    'jenis_layanan_kode'=>$q->jnsPelayanan,
                    'jenis_layanan_nama'=>$q->jnsPelayanan==1 ? 'Rawat Inap' : 'Rawat Jalan',
                    'cara_keluar'=>[
                        'id'=>$q->perawatan->caraKeluar->kode.'!#!'.$q->perawatan->caraKeluar->nama,
                        'nama'=>$q->perawatan->caraKeluar->nama,
                    ],
                    'kelas_rawat'=>[
                        'id'=>$q->perawatan->kelasRawat->kode.'!#!'.$q->perawatan->kelasRawat->nama,
                        'nama'=>$q->perawatan->kelasRawat->nama,
                    ],
                    'kondisi_pulang'=>[
                        'id'=>$q->perawatan->kondisiPulang->kode.'!#!'.$q->perawatan->kondisiPulang->nama,
                        'nama'=>$q->perawatan->kondisiPulang->nama,
                    ],
                    'ruang_rawat'=>[
                        'id'=>$q->perawatan->ruangRawat->kode.'!#!'.$q->perawatan->ruangRawat->nama,
                        'nama'=>$q->perawatan->ruangRawat->nama,
                    ],
                    'spesialistik'=>[
                        'id'=>$q->perawatan->spesialistik->kode.'!#!'.$q->perawatan->spesialistik->nama,
                        'nama'=>$q->perawatan->spesialistik->nama,
                    ],
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function validateData($req)
    {
        $var = ['jaminan'=>'Jaminan','poli'=>'Poliklinik','ruang_rawat'=>'ruang rawat','kelas_rawat'=>'kelas rawat','spesialis'=>'spesialis','cara_keluar'=>'cara keluar','kondisi_pulang'=>'kondisi pulang','ppk'=>'Faskes','poli_kontrol'=>'Poliklinik kontrol','dpjp'=>'DPJP'];
        if(count($var)>0){
            foreach($var as $k => $v){
                if(isset($req[$k])){
                    $tmp=explode('!#!',$req[$k]);
                    if(count($tmp)!=2){
                        $this->error_msg='Format '.$v.' tidak valid';
                        return false;
                    }
                }
            }
        }

        $diagnosa_tmp=$req['diagnosa'];
        if(isset($diagnosa_tmp)>0){
            $level=array_count_values(array_column($diagnosa_tmp,'level'));
            if(count($level)>0){
                if($level[1]>1){
                    $this->error_msg='Hanya 1 diagnosa primer yang diizinkan';
                    return false;
                }
            }
        }

        return true;
    }
    function insertData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try{
                    $insert = DB::table($this->table)->insertGetId($this->db_data);
                    if($insert){
                        (new PengajuanKlaimDiagnosa())->insert($insert,$req['diagnosa']);
                        (new PengajuanKlaimProsedur())->insert($insert,$req['prosedur']);
                        if($this->vclaim->setup([
                            'url'=>'LPK/insert',
                            'method'=>'POST',
                            'param'=>$this->vclaim_data,
                        ])->run()){
                            $this->data=$this->vclaim->getResponse();
                            DB::commit();
                            return true;
                        }else{
                            $this->error_msg=$this->vclaim->error_msg;
                            DB::rollBack();
                        }
                    }else{
                        $this->error_msg='LPK gagal disimpan';
                        DB::rollBack();
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function updateData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try {
                    $update = DB::table($this->table)->where('id',$req['id'])->update($this->db_data);
                    if($update){
                        (new PengajuanKlaimDiagnosa())->insert($insert,$req['diagnosa']);
                        (new PengajuanKlaimProsedur())->insert($insert,$req['prosedur']);
                        if($this->vclaim->setup([
                            'url'=>'LPK/update',
                            'method'=>'POST',
                            'param'=>$this->vclaim_data,
                        ])->run()){
                            DB::commit();
                            $this->data=$this->vclaim->getResponse();
                            return true;
                        }else{
                            $this->error_msg=$this->vclaim->error_msg;
                            DB::rollBack();
                        }
                    }else{
                        $this->error_msg='LPK gagal diperbaharui';
                        DB::rollBack();
                    }
                } catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function deleteData($req)
    {
        DB::beginTransaction();
            try {
                $delete=DB::table($this->table)->where('no_sep',$req['no_sep'])->where('id',$req['id'])->whereNull('deleted_at')->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$req['user']]);
                if($delete){
                    if($this->vclaim->setup([
                        'url'=>'LPK/delete',
                        'method'=>'DELETE',
                        'param'=>[
                            "request"=>[
                                "t_lpk"=>[
                                    "noSep"=>(string) $req['sep']             
                                ]
                            ]
                        ]
                    ])->run()){
                        $this->data=$this->vclaim->getResponse();
                        DB::commit();
                        return true;
                    }else{
                        $this->error_msg=$this->vclaim->error_msg;
                        DB::rollBack();
                    }
                }else{
                    $this->error_msg='LPK gagal dihapus';
                    DB::rollBack();
                }
            } catch(\Illuminate\Database\QueryException $ex){ 
                $this->error_msg=$ex->getMessage();
                DB::rollBack();
            }
        return false;
    }
}