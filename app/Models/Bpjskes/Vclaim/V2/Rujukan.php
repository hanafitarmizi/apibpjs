<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Rujukan extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_rujukan';
    protected $primaryKey = 'ruj_id';

    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;

    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function formatReturnRujukan($rujukan=NULL)
    {
        $result=NULL;
        if(!empty($rujukan)){
            //check tingkat faskes
            $fs=new Referensi();
            $tingkat_faskes=1;
            if(!$fs->faskes(1,$rujukan->provPerujuk->kode)){
                $fs->faskes(2,$rujukan->provPerujuk->kode);
                $tingkat_faskes=2;
            }

            $result= [
                'no_pasien'=>$rujukan->peserta->mr->noMR,
                'no_kartu'=>$rujukan->peserta->noKartu,
                'nama'=>$rujukan->peserta->nama,
                'no_rujukan'=>$rujukan->noKunjungan,
                'tgl_rujukan'=>!empty($rujukan->tglKunjungan) ? date('d-m-Y',strtotime($rujukan->tglKunjungan)) : NULL,
                'jenis_layanan'=>$rujukan->pelayanan->kode,
                'no_telp'=>$rujukan->peserta->mr->noTelepon,
                'asal_rujukan'=>[
                    'tingkat'=>$tingkat_faskes,
                    'id'=>$rujukan->provPerujuk->kode.'!#!'.$rujukan->provPerujuk->nama,
                    'nama'=>$rujukan->provPerujuk->nama
                ],
                'diagnosa'=>[
                    'id'=>$rujukan->diagnosa->kode.'!#!'.$rujukan->diagnosa->nama,
                    'nama'=>$rujukan->diagnosa->nama,
                ],
                'cob'=>$rujukan->peserta->cob->noAsuransi!=NULL ? '1' : '0',
                'poliklinik'=>[
                    'id'=>$rujukan->poliRujukan->kode.'!#!'.$rujukan->poliRujukan->nama,
                    'nama'=>$rujukan->poliRujukan->nama,
                ],
                'keluhan'=>$rujukan->keluhan,
            ];

            if(isset($rujukan->noRujukan)){
                $result['no_rujukan']=$rujukan->noRujukan;
            }
        }
        return $result;
    }
    function searchRujukan($jenis_faskes,$jenis_cari,$nomor)
    {
        $url[]='Rujukan';
        if($jenis_faskes==2){//RS
            $url[]='RS';
        }
        if($jenis_cari==2){//by no kartu
            $url[]='Peserta';
        }elseif($jenis_cari==3){//by no kartu list
            $url[]='List/Peserta';
        }
        $url[]=str_replace(' ','',trim($nomor));
        $full_url=implode('/',$url);
        if($this->vclaim->setup([
            'url'=>$full_url,
            'method'=>'GET',
        ])->run()){
            $rujukan=$this->vclaim->getResponse();
            if($jenis_cari==3){
                $data=[];
                foreach($rujukan->rujukan as $ruj){
                    $data[]=$this->formatReturnRujukan($ruj);
                }
            }else{
                $data=$this->formatReturnRujukan($rujukan->rujukan);
            }
            $this->data=$data;
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function searchRujukanKeluar($norujukan)
    {
        if($this->vclaim->setup([
            'url'=>'Rujukan/Keluar/'.trim($norujukan),
            'method'=>'GET',
        ])->run()){
            $rj=$this->vclaim->getResponse()->rujukan;
            $this->data=[
                'no_rujukan'=>$rj->noRujukan,
                'no_sep'=>$rj->noSep,
                'no_kartu'=>$rj->noKartu,
                'nama'=>$rj->nama,
                'kelas_rawat'=>$rj->kelasRawat,
                'jkel'=>$rj->kelamin=='P' ? 'Perempuan' : 'Laki-laki',
                'tgl_lahir'=>!empty($rj->tglLahir) ? date('d-m-Y',strtotime($rj->tglLahir)) : '',
                'tgl_sep'=>!empty($rj->tglSep) ? date('d-m-Y',strtotime($rj->tglSep)) : '',
                'tgl_rujukan'=>!empty($rj->tglRujukan) ? date('d-m-Y',strtotime($rj->tglRujukan)) : '',
                'tgl_kunjungan'=>!empty($rj->tglRencanaKunjungan) ? date('d-m-Y',strtotime($rj->tglRencanaKunjungan)) : '',
                'jenis_layanan'=>$rj->jnsPelayanan==1 ? 'Rawat Inap' : 'Rawat Jalan',
                'tipe_rujukan'=>$rj->tipeRujukan==1 ? 'Partial' : ( $rj->tipeRujukan==2 ? 'Rujuk Balik (PRB)' : 'Penuh' ),
                'ppk_dirujuk'=>[
                    'id'=>$rj->ppkDirujuk.'!#!'.$rj->namaPpkDirujuk,
                    'nama'=>$rj->namaPpkDirujuk
                ],
                'poliklinik'=>[
                    'id'=>$rj->poliRujukan.'!#!'.$rj->namaPoliRujukan,
                    'nama'=>$rj->namaPoliRujukan
                ],
                'diagnosa'=>[
                    'id'=>$rj->diagRujukan.'!#!'.trim($rj->namaDiagRujukan),
                    'nama'=>trim($rj->namaDiagRujukan),
                ],
                'catatan'=>$rj->catatan,
            ];
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function validateData($req)
    {
        $var = ['ppk_dirujuk'=>'PPK Dirujuk','diagnosa'=>'Diagnosa','poli_rujukan'=>'Poliklinik rujukan'];
        if(count($var)>0){
            foreach($var as $k => $v){
                if(!empty($req[$k])){
                    $tmp=explode('!#!',$req[$k]);
                    if(count($tmp)!=2){
                        $this->error_msg='Format '.$v.' tidak valid';
                        return false;
                    }
                }
            }
        }
        return true;
    }
    function prepareData($req,$is_new=true)
    {
        $ppk_dirujuk=explode('!#!',$req['ppk_dirujuk']);
        $diagnosa=explode('!#!',$req['diagnosa']);

        if($req['jenis_layanan']==2){
            $poli=explode('!#!',$req['poli_rujukan']);
        }
        
        $this->vclaim_data=[
            'request'=>[
                't_rujukan'=>[
                    'noSep'=>$req['no_sep'],
                    'tglRujukan'=>date('Y-m-d',strtotime($req['tgl_rujukan'])),
                    "tglRencanaKunjungan"=>date('Y-m-d',strtotime($req['tgl_kunjungan'])),
                    "ppkDirujuk"=>$ppk_dirujuk[0],
                    "jnsPelayanan"=>$req['jenis_layanan'],
                    "catatan"=>$req['catatan'],
                    "diagRujukan"=>$diagnosa[0],
                    "tipeRujukan"=>$req['tipe_rujukan'],
                    "poliRujukan"=>$req['jenis_layanan']==2 ? $poli[0] : '', 
                    "user"=>setUser($req['user']),
                ]
            ]
        ];
        
        $this->db_data=[
            'ruj_pasien_kode'=>$req['no_pasien'],
            'ruj_reg_kode'=>$req['no_daftar'],
            'ruj_no_sep'=>$req['no_sep'],
            'ruj_tgl_rujukan'=>date('Y-m-d',strtotime($req['tgl_rujukan'])),
            'ruj_tgl_kunjungan'=>date('Y-m-d',strtotime($req['tgl_kunjungan'])),
            'ruj_ppk_dirujuk_tingkat'=>$req['ppk_dirujuk_tingkat'],
            'ruj_ppk_dirujuk_kode'=>$ppk_dirujuk[0],
            'ruj_ppk_dirujuk_nama'=>$ppk_dirujuk[1],
            'ruj_jenis_pelayanan'=>$req['jenis_layanan'],
            'ruj_catatan'=>$req['catatan'],
            'ruj_diagnosa_kode'=>$diagnosa[0],
            'ruj_diagnosa_nama'=>$diagnosa[1],
            'ruj_simrs_diagnosa_kode'=>(new MappingDiagnosa())->getDiagnosaSimrs($diagnosa[0]),
            'ruj_tipe_rujukan'=>$req['tipe_rujukan'],
            'ruj_poli_kode'=>$req['jenis_layanan']==2 ? $poli[0] : '', 
            'ruj_poli_nama'=>$req['jenis_layanan']==2 ? $poli[1] : '', 
            'ruj_simrs_poli_kode'=>$req['jenis_layanan']==2 ? (new MappingPoli())->getPoliSimrs($poli[0]) : '', 
        ];
        if($is_new){
            $this->db_data['ruj_created_at']=date('Y-m-d H:i:s');
            $this->db_data['ruj_created_by']=$req['user'];
        }else{
            $this->vclaim_data['request']['t_rujukan']['noRujukan']=$req['no_rujukan'];
            $this->db_data['ruj_updated_at']=date('Y-m-d H:i:s');
            $this->db_data['ruj_updated_by']=$req['user'];
        }
    }
    function saveRujukan($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try{
                    $insert = DB::table($this->table)->insertGetId($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'Rujukan/2.0/insert',
                        'method'=>'POST',
                        'param'=>$this->vclaim_data,
                    ])->run()){
                        $data=$this->vclaim->getResponse();
                        $update=DB::table($this->table)->where('ruj_id','=',$insert)->update(['ruj_no_rujukan'=>$data->rujukan->noRujukan]);
                        $this->data=['id'=>$insert,'nomor'=>$data->rujukan->noRujukan];
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
    function updateRujukan($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req,false);
            DB::beginTransaction();
                try{
                    $update = DB::table($this->table)->where('ruj_id','=',$req['id'])->update($this->db_data);
                    if($this->vclaim->setup([
                        'url'=>'Rujukan/2.0/Update',
                        'method'=>'PUT',
                        'param'=>$this->vclaim_data,
                    ])->run()){
                        $this->data=$req['id'];
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
    function deleteRujukan($req)
    {
        DB::beginTransaction();
            try{
                $update=DB::table($this->table)->where('id',$req['id'])->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$req['user']]);
                if($this->vclaim->setup([
                    'url'=>'Rujukan/delete',
                    'method'=>'DELETE',
                    'param'=>[
                        'request'=>[
                            't_rujukan'=>[
                                'noRujukan'=>trim($req['no_rujukan']),
                                'user'=>setUser(trim($req['user'])),
                            ]
                        ]
                    ],
                ])->run()){
                    $this->data=$update;
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
    function getListSpesialis($ppk,$tgl)
    {
        $ppk=explode('!#!',$ppk);
        if($this->vclaim->setup([
            'url'=>'Rujukan/ListSpesialistik/PPKRujukan/'.trim($ppk[0]).'/TglRujukan/'.trim(date('Y-m-d',strtotime($tgl))),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->vclaim->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>$q->kodeSpesialis.'!#!'.$q->namaSpesialis,'nama'=>$q->namaSpesialis,'kapasitas'=>$q->kapasitas,'jml_rujukan'=>$q->jumlahRujukan,'persentase'=>$q->persentase];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function getListSarana($ppk)
    {
        if($this->vclaim->setup([
            'url'=>'Rujukan/ListSarana/PPKRujukan/'.trim($ppk),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->vclaim->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>$q->kodeSarana.'!#!'.$q->namaSarana,'nama'=>$q->namaSarana];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
}