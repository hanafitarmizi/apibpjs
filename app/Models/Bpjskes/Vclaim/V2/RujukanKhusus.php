<?php
namespace App\Models\Bpjskes\Vclaim\V2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class RujukanKhusus extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bpjskes_rujukan_khusus';
    protected $primaryKey = 'rjk_id';

    public $vclaim,$error_msg,$vclaim_data,$db_data,$data;

    function __construct()
    {
        $this->vclaim=new BaseVclaim();
    }
    function formatReturnRujukan($rujukan=NULL)
    {
        $result=NULL;
        if(!empty($rujukan)){
            $result=[
                'no_rujukan'=>$rujukan->norujukan,
                'no_kartu'=>$rujukan->nokapst,
                'nama_pasien'=>$rujukan->nmpst,
                'diagnosa'=>$rujukan->diagppk,
                'tgl_awal_rujukan'=>date('Y-m-d',strtotime($rujukan->tglrujukan_awal)),
                'tgl_akhir_rujukan'=>date('Y-m-d',strtotime($rujukan->tglrujukan_berakhir)),
            ];
        }
        return $result;
    }
    function validateData($req)
    {
        //check format diagnosa, prosedur
        $var=['diagnosa_primer'=>'Diagnosa Primer','diagnosa_sekunder'=>'Diagnosa Sekunder','prosedur'=>'Prosedur'];
        foreach($var as $k => $d){
            if(isset($req[$k])){
                $tmp=explode('!#!',$req[$k]);
                if(count($tmp)!=2){
                    $this->error_msg="Format ".$d." tidak valid.";
                    return false;
                }
            }
        }
        return true;
    }
    function prepareData($req,$is_new=true)
    {
        $prosedur=explode('!#!',$req['prosedur']);
        $diagnosa_primer=explode('!#!',$req['diagnosa_primer']);
        $diagnosa_sekunder=explode('!#!',$req['diagnosa_sekunder']);
        $this->vclaim_data=[
            'noRujukan'=>$req['no_rujukan'],
            'diagnosa'=>[
                ['kode'=>"P;".$diagnosa_primer[0]],
                ['kode'=>"S;".$diagnosa_sekunder[0]],
            ],
            'procedure'=>[
                ['kode'=>$prosedur[0]]
            ],
            'user'=>setUser($req['user']),
        ];
        $this->db_data=[
            'rjk_pasien_kode'=>$req['no_pasien'],
            'rjk_reg_kode'=>$req['no_daftar'],
            'rjk_no_rujukan'=>$req['no_rujukan'],
            'rjk_bpjs_diagnosa_primer_kode'=>$diagnosa_primer[0],
            'rjk_bpjs_diagnosa_primer_nama'=>$diagnosa_primer[1],
            'rjk_bpjs_diagnosa_sekunder_kode'=>$diagnosa_sekunder[0],
            'rjk_bpjs_diagnosa_sekunder_nama'=>$diagnosa_sekunder[1],
            'rjk_bpjs_prosedur_kode'=>$prosedur[0],
            'rjk_bpjs_prosedur_nama'=>$prosedur[1],
            'rjk_created_at'=>date('Y-m-d H:i:s'),
            'rjk_created_by'=>$req['user'],
        ];
    }
    function listRujukan($req)
    {
        if($this->vclaim->setup([
            'url'=>'Rujukan/Khusus/List/Bulan/'.trim($req['bulan']).'/Tahun/'.trim($req['tahun']),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->vclaim->getResponse();

            foreach($tmp['rujukan'] as $q){

                //get diagnosa
                // $diagnosa=NULL;
                // $r = new Referensi();
                // if($r->diagnosa($q->diagppk)){
                //     $diagnosa=array_map(function($qa) use ($q){
                //         $t=explode('!#!',$qa['id'])[0];
                //         return $t==$q->diagppk ? $qa['id'] : NULL;
                //     },$r->data);
                // }

                $this->data[]=['id'=>$q->idrujukan,'no_rujukan'=>$q->norujukan,'no_kartu'=>$q->nokapst,'nama'=>$q->nmpst,'diagnosa'=>$q->diagppk,'tgl_awal_rujukan'=>date('d-m-Y',strtotime($q->tglrujukan_awal)),'tgl_akhir_rujukan'=>date('d-m-Y',strtotime($q->tglrujukan_berakhir))];
            }
            return true;
        }else{
            $this->error_msg=$this->vclaim->error_msg;
            return false;
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
                        'url'=>'Rujukan/Khusus/insert',
                        'method'=>'POST',
                        'param'=>$this->vclaim_data,
                    ])->run()){
                        $data=$this->formatReturnRujukan($this->vclaim->getResponse()->rujukan);
                        DB::table($this->table)->where('rjk_id',$insert)->update(['rjk_tgl_awal_rujukan'=>$data['tgl_awal_rujukan'],'rjk_tgl_akhir_rujukan'=>$data['tgl_akhir_rujukan']]);
                        $this->data=$data;
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
                $update=DB::table($this->table)->where('rjk_no_rujukan',$req['no_rujukan'])->whereNull('rjk_deleted_at')->update(['rjk_deleted_at'=>date('Y-m-d H:i:s'),'rjk_deleted_by'=>$req['user']]);
                if($this->vclaim->setup([
                    'url'=>'Rujukan/Khusus/delete',
                    'method'=>'DELETE',
                    'param'=>[
                        'request'=>[
                            't_rujukan'=>[
                                'idRujukan'=>trim($req['id_rujukan']),
                                'noRujukan'=>trim($req['no_rujukan']),
                                'user'=>setUser($req['user']),
                            ]
                        ]
                    ],
                ])->run()){
                    $this->data="OK";
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