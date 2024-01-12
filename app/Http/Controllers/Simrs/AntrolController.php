<?php
namespace App\Http\Controllers\Simrs;
use Illuminate\Support\Facades\DB;
use App\Models\Bpjskes\Antrol\V2\Antrian;
class AntrolController extends BaseController
{
    function pushAntrol()
    {
        $antrean=DB::table('bpjskes_antrol_antrean')->where('aa_status_kirim','=',0)->orWhere('aa_status_kirim','=',2)->get();
        if(count($antrean)>0){
            foreach($antrean as $an){
                $a = new Antrian();
                if($a->save([
                    'kode'=>$an->aa_kode_booking,
                    'jenis_pasien'=>$an->aa_jenis_pasien,
                    'no_pasien'=>$an->aa_no_pasien,
                    'no_kartu'=>$an->aa_no_kartu,
                    'no_referensi'=>$an->aa_no_referensi,
                    'nik'=>$an->aa_nik,
                    'no_hp'=>$an->aa_no_hp,
                    'poliklinik'=>$an->aa_bpjs_poli_kode.'!#!'.$an->aa_bpjs_poli_nama,
                    'pasien_baru'=>$an->aa_pasien_baru,
                    'tgl_periksa'=>$an->aa_tgl_periksa!=NULL ? date('d-m-Y',strtotime($an->aa_tgl_periksa)) : NULL,
                    'dokter'=>$an->aa_bpjs_dokter_kode.'!#!'.$an->aa_bpjs_dokter_nama,
                    'jam_praktek'=>$an->aa_jam_praktek,
                    'jenis_kunjungan'=>$an->aa_jenis_kunjungan,
                    'no_antrian'=>$an->aa_no_antrian,
                    'angka_antrian'=>$an->aa_no_antrian_angka,
                    'estimasi_dilayani'=>$an->aa_estimasi_layanan,
                    'kuota_jkn'=>$an->aa_kuota_jkn,
                    'sisa_kuota_jkn'=>$an->aa_sisa_kuota_jkn,
                    'kuota_non_jkn'=>$an->aa_kuota_non_jkn,
                    'sisa_kuota_non_jkn'=>$an->aa_sisa_kuota_non_jkn,
                    'ket'=>$an->aa_ket,
                ])){
                    DB::table('bpjskes_antrol_antrean')->where('aa_id',$an->aa_id)->update(['aa_status_kirim'=>1]);
                }else{
                    DB::table('bpjskes_antrol_antrean')->where('aa_id',$an->aa_id)->update(['aa_status_kirim'=>2,'aa_log'=>$a->error_msg]);
                }
            }
        }
        $task=DB::table('bpjskes_antrol_antrean_layanan')->where('aal_status_kirim','=',0)->get();
        if(count($task)>0){
            foreach($task as $t){
                echo date('d-m-Y',$t->aal_waktu);
                $tt=[
                    'kode'=>$t->aal_kode_booking,
                    'aktifitas'=>$t->aal_task_id,
                    'waktu'=>$t->aal_waktu,
                ];
                $an = new Antrian();
                if($an->updateWaktu($tt)){
                    DB::table('bpjskes_antrol_antrean_layanan')->where('aal_id',$t->aal_id)->update(['aal_status_kirim'=>1]);
                }else{
                    DB::table('bpjskes_antrol_antrean_layanan')->where('aal_id',$an->aal_id)->update(['aal_status_kirim'=>2,'aal_log'=>$a->error_msg]);
                }
            }
        }
        $batal=DB::table('bpjskes_antrol_antrean_batal')->where('aab_status_kirim','=',0)->get();
        if(count($batal)>0){
            foreach($batal as $b){
                $bt = new Antrian();
                if($bt->batalAntrian([
                    'kode'=>$b->aab_kode_booking,
                    'ket'=>$b->aab_ket,
                ])){
                    DB::table('bpjskes_antrol_antrean_batal')->where('aab_id',$b->aab_id)->update(['aab_status_kirim'=>1]);
                }else{
                    DB::table('bpjskes_antrol_antrean_batal')->where('aab_id',$an->aab_id)->update(['aab_status_kirim'=>2,'aab_log'=>$a->error_msg]);
                }
            }
        }
    }
}