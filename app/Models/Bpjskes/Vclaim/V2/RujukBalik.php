<?php
namespace App\Models\Bpjskes\Vclaim\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class RujukBalik extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_rujuk_balik';
    protected $primaryKey = 'prb_id';
    public $vclaim,$error_msg,$data,$vclaim_data,$db_data;
    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function searchByNomor($no_srb,$no_sep)
    {
        if($this->vclaim->setup([
            'url'=>'prb/'.trim($no_srb).'/nosep/'.trim($no_sep),
            'method'=>'GET'
        ])->run()){
            $r=$this->getResponse();
            foreach($r->prb as $q){

                $obat=NULL;
                foreach($q->obat->obat as $qq){
                    $obat[]=[
                        'id'=>$qq->kdObat.'!#!'.$qq->nmObat,
                        'nama'=>$qq->nmObat,
                        'jml_obat'=>$qq->jmlObat,
                        'signa1'=>$qq->signa1,
                        'signa2'=>$qq->signa2,
                    ];
                }

                $this->data[]=[
                    'no_sep'=>$q->noSEP,
                    'no_srb'=>$q->noSRB,
                    'nama'=>$q->peserta->nama,
                    'no_kartu'=>$q->peserta->noKartu,
                    'email'=>$q->peserta->email,
                    'no_telp'=>$q->peserta->noTelepon,
                    'jkel'=>$q->peserta->kelamin=='P' ? 'Perempuan' : 'Laki-laki',
                    'tgl_lahir'=>$q->peserta->tglLahir!=NULL ? date('d-m-Y',strtotime($q->peserta->tglLahir)) : NULL,
                    'alamat'=>$q->peserta->alamat,
                    'faskes_asal'=>[
                        'id'=>$q->peserta->asalFaskes->kode.'!#!'.$q->peserta->asalFaskes->nama,
                        'nama'=>$q->peserta->asalFaskes->nama,
                    ],
                    'dpjp'=>[
                        'id'=>$q->DPJP->kode.'!#!'.$q->DPJP->nama,
                        'nama'=>$q->DPJP->nama,
                    ],
                    'program_prb'=>[
                        'id'=>$q->programPRB->kode.'!#!'.$q->programPRB->nama,
                        'nama'=>$q->programPRB->nama,
                    ],
                    'obat'=>$obat,
                    'ket'=>$q->keterangan,
                    'saran'=>$q->saran,
                    'tgl_lahir'=>$q->tglSRB!=NULL ? date('d-m-Y',strtotime($q->tglSRB)) : NULL,
                ];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
        }
    }
    function searchByTgl($no_srb,$no_sep)
    {
        if($this->vclaim->setup([
            'url'=>'prb/tglMulai/'.date('Y-m-d',strtotime($tgl_mulai)).'/tglAkhir/'.date('Y-m-d',strtotime($tgl_akhir)),
            'method'=>'GET'
        ])->run()){
            $r=$this->getResponse();
            foreach($r->prb->list as $q){

                $obat=NULL;
                foreach($q->obat->obat as $qq){
                    $obat[]=[
                        'id'=>$qq->kdObat.'!#!'.$qq->nmObat,
                        'nama'=>$qq->nmObat,
                        'jml_obat'=>$qq->jmlObat,
                        'signa1'=>$qq->signa1,
                        'signa2'=>$qq->signa2,
                    ];
                }

                $this->data[]=[
                    'no_sep'=>$q->noSEP,
                    'no_srb'=>$q->noSRB,
                    'nama'=>$q->peserta->nama,
                    'no_kartu'=>$q->peserta->noKartu,
                    'email'=>$q->peserta->email,
                    'no_telp'=>$q->peserta->noTelepon,
                    'jkel'=>$q->peserta->kelamin=='P' ? 'Perempuan' : 'Laki-laki',
                    'tgl_lahir'=>$q->peserta->tglLahir!=NULL ? date('d-m-Y',strtotime($q->peserta->tglLahir)) : NULL,
                    'alamat'=>$q->peserta->alamat,
                    'faskes_asal'=>[
                        'id'=>$q->peserta->asalFaskes->kode.'!#!'.$q->peserta->asalFaskes->nama,
                        'nama'=>$q->peserta->asalFaskes->nama,
                    ],
                    'dpjp'=>[
                        'id'=>$q->DPJP->kode.'!#!'.$q->DPJP->nama,
                        'nama'=>$q->DPJP->nama,
                    ],
                    'program_prb'=>[
                        'id'=>$q->programPRB->kode.'!#!'.$q->programPRB->nama,
                        'nama'=>$q->programPRB->nama,
                    ],
                    'obat'=>$obat,
                    'ket'=>$q->keterangan,
                    'saran'=>$q->saran,
                    'tgl_lahir'=>$q->tglSRB!=NULL ? date('d-m-Y',strtotime($q->tglSRB)) : NULL,
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
        $var = ['program'=>'Program','dpjp'=>'DPJP'];
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
        return true;
    }
    function prepareData($req,$is_new=true)
    {
        $dpjp=explode('!#!',$req['dpjp']);
        $program=explode('!#!',$req['program']);

        $this->vclaim_data=[
            'request'=>[
                't_prb'=>[
                    'noSep'=>$req['no_sep'],
                    'noKartu'=>$req['no_kartu'],
                    'alamat'=>$req['alamat'],
                    'email'=>$req['email'],
                    'programPRB'=>$program[0],
                    'kodeDPJP'=>$dpjp[0],
                    'keterangan'=>$req['keterangan'],
                    'saran'=>$req['saran'],
                    'user'=>setUser($req['user']),
                ]
            ]
        ];
        //set obat 
        if(isset($req['obat'])){
            $obat=[];
            foreach($req['obat'] as $o){
                $to=explode('!#!',$o['obat']);
                $obat[]=[
                    'kdObat'=>$to[0],
                    'signa1'=>$o['signa1'],
                    'signa2'=>$o['signa2'],
                    'jmlObat'=>$o['jml'],
                ];
            }
            $this->vclaim_data['request']['t_prb']['obat']=$obat;
        }
        if(!$is_new){
            $this->vclaim_data['request']['t_prb']['noSrb']=$req['no_srb'];
        }
        
        $this->db_data=[
            'prb_pasien_kode'=>$req['no_pasien'],
            'prb_reg_kode'=>$req['no_daftar'],
            'prb_no_sep'=>$req['no_sep'],
            'prb_no_kartu'=>$req['no_kartu'],
            'prb_alamat'=>$req['alamat'],
            'prb_email'=>$req['email'],
            'prb_bpjs_program_kode'=>$program[0],
            'prb_bpjs_program_nama'=>$program[1],
            'prb_bpjs_dpjp_kode'=>$dpjp[0],
            'prb_bpjs_dpjp_nama'=>$dpjp[1],
            'prb_ket'=>$req['keterangan'],
            'prb_saran'=>$req['saran'],
        ];

        if($is_new){
            $this->db_data['prb_created_at']=date('Y-m-d H:i:s');
            $this->db_data['prb_created_by']=$req['user'];
        }else{
            $this->db_data['prb_nomor']=$req['no_srb'];
            $this->db_data['prb_updated_at']=date('Y-m-d H:i:s');
            $this->db_data['prb_updated_by']=$req['user'];
        }
    }
    function insertData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req);
            DB::beginTransaction();
                try{
                    $insert=DB::table($this->table)->insertGetId($this->db_data);
                    (new RujukBalikObat())->insert($insert,$req['obat']);
                    if($this->vclaim->setup([
                        'url'=>'PRB/insert',
                        'method'=>'POST',
                        'param'=>$this->vclaim_data
                    ])->run()){
                        $result=$this->vclaim->getResponse();
                        DB::table($this->table)->where('prb_id',$insert)->update(['prb_nomor'=>$result->noSRB,'prb_tgl_srb'=>date('Y-m-d',strtotime($result->tglSRB))]);
                        $this->data=['id'=>$insert,'nomor'=>$result->noSRB];
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
    function updateData($req)
    {
        if($this->validateData($req)){
            $this->prepareData($req,false);
            DB::beginTransaction();
                try{
                    $update=DB::table($this->table)->where('prb_id',$req['id'])->update($this->db_data);
                    if($update){
                        (new RujukBalikObat())->insert($req['id'],$req['obat']);
                        if($this->vclaim->setup([
                            'url'=>'PRB/Update',
                            'method'=>'PUT',
                            'param'=>$this->vclaim_data
                        ])->run()){
                            $result=$this->vclaim->getResponse();
                            DB::commit();
                            $this->data=['id'=>$req['id'],'nomor'=>$result];
                            return true;
                        }else{
                            $this->error_msg=$this->vclaim->error_msg;
                            DB::rollBack();
                        }
                    }
                }catch(\Illuminate\Database\QueryException $ex){ 
                    $this->error_msg=$ex->getMessage();
                    DB::rollBack();
                }
        }
        return false;
    }
    function deleteData($req)
    {
        DB::beginTransaction();
            try{
                $update=DB::table($this->table)->where('prb_id',$req['id'])->update(['prb_deleted_at'=>date('Y-m-d H:i:s'),'prb_deleted_by'=>$req['user']]);
                if($this->vclaim->setup([
                    'url'=>'PRB/Delete',
                    'method'=>'DELETE',
                    'param'=>[
                        'request'=>[
                            't_prb'=>[
                                'noSrb'=>$req['no_srb'],
                                'noSep'=>$req['no_sep'],
                                'user'=>setUser($req['user']),
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
            }catch(\Illuminate\Database\QueryException $ex){ 
                $this->error_msg=$ex->getMessage();
                DB::rollBack();
            }
        return false;
    }
}