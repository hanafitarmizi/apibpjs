<?php
namespace App\Models\Bpjskes\Vclaim\V2;
class Monitoring extends BaseVclaim
{
    public $error_msg,$data;

    function kunjungan($tgl,$jenis)
    {
        if($this->setup([
            'url'=>'Monitoring/Kunjungan/Tanggal/'.$tgl.'/JnsPelayanan/'.$jenis,
            'method'=>'GET'
        ])->run()){
            $tmp=$this->getResponse();
            foreach($tmp->sep as $q){

                //diagnosa
                $diagnosa=NULL;
                if(!empty($q->diagnosa)){
                    $r = new Referensi();
                    if($r->diagnosa($q->diagnosa)){
                        $diagnosa=$r->data;
                    }
                }

                $this->data[]=[
                    'nama'=>$q->nama,
                    'no_kartu'=>$q->noKartu,
                    'no_sep'=>$q->noSep,
                    'no_rujukan'=>$q->noRujukan,
                    'diagnosa'=>$diagnosa,
                    'jenis_layanan'=>[
                        'id'=>$q->jnsPelayanan=='R.Inap' ? 1 : 2,
                        'nama'=>$q->jnsPelayanan=='R.Inap' ? 'Rawat Inap' : 'Rawat Jalan',
                    ],
                    'kelas_rawat',
                    'tgl_sep'=>$q->tglSep!=NULL ? date('d-m-Y',strtotime($q->tglSep)) : NULL,
                    'tgl_sep_pulang'=>$q->tglPlgSep!=NULL ? date('d-m-Y',strtotime($q->tglPlgSep)) : NULL,
                ];
            }
            return true;
        }
        return false;
    }
    function klaim($tgl,$jenis,$status)
    {
        if($this->setup([
            'url'=>'Monitoring/Klaim/Tanggal/'.date('Y-m-d',strtotime($tgl)).'/JnsPelayanan/'.$jenis.'/status/'.$status,
            'method'=>'GET'
        ])->run()){
            $tmp=$this->getResponse();
            foreach($tmp->klaim as $q){

                //poliklinik
                $poliklinik=NULL;
                if(!empty($q->poli)){
                    $r = new Referensi();
                    if($r->poliklinik($q->poli)){
                        $poliklinik=$r->matchByName($r->data);
                    }
                }

                $this->data[]=[
                    'no_sep'=>$q->noSEP,
                    'inacbg'=>[
                        'id'=>trim($q->Inacbg->kode).'!#!'.trim($q->Inacbg->nama),
                        'nama'=>trim($q->Inacbg->nama),
                    ],
                    'biaya'=>[
                        'biaya_pengajuan'=>$q->biaya->byPengajuan,
                        'status_disetujui'=>$q->biaya->bySetujui,
                        'tarif_grouper'=>$q->biaya->byTarifGruper,
                        'tarif_rs'=>$q->biaya->byTarifRS,
                        'top_up'=>$q->biaya->byTopup,
                    ],
                    'kelas_rawat'=>$q->kelasRawat,
                    'poliklinik'=>$poliklinik,
                    'no_fpk'=>$q->noFPK,
                    'status'=>$q->status,
                    'tgl_pulang'=>$q->tglPulang!=NULL ? date('d-m-Y',strtotime($q->tglPulang)) : NULL,
                    'tgl_sep'=>$q->tglSep!=NULL ? date('d-m-Y',strtotime($q->tglSep)) : NULL,
                ];
            };
            return true;
        }
        return false;
    }
    function historipelayanan($nomor,$tgl_mulai,$tgl_akhir)
    {
        if($this->setup([
            'url'=>'monitoring/HistoriPelayanan/NoKartu/'.$nomor.'/tglMulai/'.date('Y-m-d',strtotime($tgl_mulai)).'/tglAkhir/'.date('Y-m-d',strtotime($tgl_akhir)),
            'method'=>'GET'
        ])->run()){
            $tmp=$this->getResponse();
            
            foreach($tmp->histori as $q){

                $poliklinik=NULL;
                if(!empty($q->poli)){
                    $p = new Referensi();
                    if($p->poliklinik($q->poli)){
                        $poliklinik=$p->matchByName($q->poli);
                    }
                }

                $ppk_pelayanan=NULL;
                if(!empty($q->ppkPelayanan)){
                    $p = new Referensi();
                    if(!$p->faskes(1,$q->ppkPelayanan)){
                        $p->faskes(2,$q->ppkPelayanan);
                    }
                    $ppk_pelayanan=$p->data!=NULL ? $p->data[0] : NULL;
                }

                $peserta=NULL;
                if(!empty($q->noKartu)){
                    $ps = new Peserta();
                    if($ps->getdata($q->noKartu,2)){
                        $peserta=$ps->data;
                    }
                }

                $rujukan=NULL;
                if(!empty($t->noRujukan)){
                    $norujukan=str_replace('/','',$t->noRujukan);
                    $r = new Rujukan();
                    if(!$r->searchRujukan(1,1,$norujukan)){
                        $r->searchRujukan(2,1,$norujukan);
                    }
                    if(isset($r->data) && $r->data!=NULL){
                        $rujukan=$r->data;
                    }
                }
          
                $diagnosa=explode('-',$q->diagnosa);
                $this->data[]=[
                    'nama'=>$q->namaPeserta,
                    'no_kartu'=>$q->noKartu,
                    'no_pasien'=>$peserta!=NULL ? $peserta['no_pasien'] : NULL,
                    'tgl_lahir'=>$peserta!=NULL ? $peserta['tgl_lahir'] : NULL,
                    'no_sep'=>$q->noSep,
                    'no_rujukan'=>$q->noRujukan,
                    'no_telp'=>$peserta!=NULL ? $peserta['no_telp'] : NULL,
                    'kelas'=>$peserta!=NULL ? $peserta['kelas'] : NULL,
                    'kelas_rawat'=>$q->kelasRawat,
                    'jenis_layanan'=>[
                        'id'=>$q->jnsPelayanan,
                        'nama'=>$q->jnsPelayanan==1 ? 'Rawat Inap' : 'Rawat Jalan',
                    ],
                    'rujukan'=>$rujukan,
                    'ppk_pelayanan'=>$ppk_pelayanan,
                    'diagnosa'=>[
                        'id'=>trim($diagnosa[0]).'!#!'.trim($diagnosa[1]),
                        'nama'=>trim($diagnosa[1]),
                    ],
                    'poliklinik'=>$poliklinik,
                    'tgl_sep'=>$q->tglSep!=NULL ? date('d-m-Y',strtotime($q->tglSep)) : NULL,
                    'tgl_sep_plg'=>$q->tglPlgSep!=NULL ? date('d-m-Y',strtotime($q->tglPlgSep)) : NULL,
                ];
            }
            return true;
        }
        return false;
    }
    function klaimjasaraharja($jenis,$tgl_mulai,$tgl_akhir)
    {
        if($this->setup([
            'url'=>'monitoring/JasaRaharja/JnsPelayanan/'.trim($jenis).'/tglMulai/'.date('Y-m-d',strtotime($tgl_mulai)).'/tglAkhir/'.date('Y-m-d',strtotime($tgl_akhir)),
            'method'=>'GET'
        ])->run()){
            $r=$this->getResponse();
            foreach($r->jaminan as $q){

                $poliklinik=NULL;
                $p = new poliklinik();
                if($p->poliklinik($q->sep->poli)){
                    $poliklinik=$p->matchByName($p->data);
                }

                $diagnosa=NULL;
                $p = new poliklinik();
                if($p->diagnosa($q->sep->diagnosa)){
                    $diagnosa=$p->matchByName($p->data);
                }

                $this->data[]=[
                    'no_pasien'=>$q->sep->peserta->noMR,
                    'no_kartu'=>$q->sep->peserta->noKartu,
                    'nama'=>$q->sep->peserta->nama,
                    'no_sep'=>$q->sep->noSEP,
                    'jenis_layanan'=>[
                        'id'=>$q->jnsPelayanan,
                        'nama'=>$q->jnsPelayanan==1 ? 'Rawat Inap' : 'Rawat Jalan',
                    ],
                    'poliklinik'=>$poliklinik,
                    'diagnosa'=>$diagnosa,
                    'tgl_sep'=>$q->sep->tglSep!=NULL ? date('d-m-Y',strtotime($q->sep->tglSep)) : NULL,
                    'tgl_sep_plg'=>$q->sep->tglPlgSep!=NULL ? date('d-m-Y',strtotime($q->sep->tglPlgSep)) : NULL,
                    'jasa_raharja'=>[
                        'no_register'=>$q->jasaRaharja->noRegister,
                        'ket_status_dijamin'=>$q->jasaRaharja->ketStatusDijamin,
                        'ket_status_dikirim'=>$q->jasaRaharja->ketStatusDikirim,
                        'biaya_dijamin'=>$q->jasaRaharja->biayaDijamin,
                        'plafon'=>$q->jasaRaharja->plafon,
                        'jml_dibayar'=>$q->jasaRaharja->jmlDibayar,
                        'tgl_kejadian'=>$q->jasaRaharja->tglKejadian!=NULL ? date('d-m-Y',strtotime($q->jasaRaharja->tglKejadian)) : NULL,
                        'status'=>$q->jasaRaharja->resultsJasaRaharja,
                    ]
                ];
            }
            return true;
        }
        return false;
    }
}