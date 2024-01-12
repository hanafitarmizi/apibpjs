<?php
namespace App\Models\Bpjskes\Vclaim\V2;

class Peserta extends BaseVclaim
{
    public $data;
    function formatReturnPeserta($q)
    {
        $result=[
            'cob'=>[
                'nama_asuransi'=>$q->peserta->cob->nmAsuransi,
                'nomor_asuransi'=>$q->peserta->cob->noAsuransi,
                'tgl_tat'=>$q->peserta->cob->tglTAT!=NULL ? date('d-m-Y',strtotime($q->peserta->cob->tglTAT)) : NULL,
                'tgl_tmt'=>$q->peserta->cob->tglTMT!=NULL ? date('d-m-Y',strtotime($q->peserta->cob->tglTMT)) : NULL,
            ],
            'kelas'=>[
                'id'=>$q->peserta->hakKelas->kode.'!#!'.$q->peserta->hakKelas->keterangan,
                'nama'=>$q->peserta->hakKelas->keterangan,
            ],
            'informasi'=>[
                'dinsos'=>$q->peserta->informasi->dinsos,
                'no_sktm'=>$q->peserta->informasi->noSKTM,
                'prolanis_prb'=>$q->peserta->informasi->prolanisPRB,
            ],
            'jenis_peserta'=>[
                'id'=>$q->peserta->jenisPeserta->kode.'!#!'.$q->peserta->jenisPeserta->keterangan,
                'nama'=>$q->peserta->jenisPeserta->keterangan,
            ],
            'nama'=>$q->peserta->nama,
            'nik'=>$q->peserta->nik,
            'no_kartu'=>$q->peserta->noKartu,
            'no_pasien'=>$q->peserta->mr->noMR,
            'tgl_lahir'=>$q->peserta->tglLahir!=NULL ? date('d-m-Y',strtotime($q->peserta->tglLahir)) : NULL,
            'jkel'=>$q->peserta->sex,
            'status'=>[
                'id'=>$q->peserta->statusPeserta->kode,
                'nama'=>$q->peserta->statusPeserta->keterangan,
            ],
            'no_telp'=>$q->peserta->mr->noTelepon,
            'pisa'=>$q->peserta->pisa,
            'faskes_rujukan'=>[
                'id'=>$q->peserta->provUmum->kdProvider,
                'nama'=>$q->peserta->provUmum->nmProvider,
            ],
            'tgl_cetak_kartu'=>$q->peserta->tglCetakKartu!=NULL ? date('d-m-Y',strtotime($q->peserta->tglCetakKartu)) : NULL,
            'tgl_tat'=>$q->peserta->tglTAT!=NULL ? date('d-m-Y',strtotime($q->peserta->tglTAT)) : NULL,
            'tgl_tmt'=>$q->peserta->tglTMT!=NULL ? date('d-m-Y',strtotime($q->peserta->tglTMT)) : NULL,
            'umur'=>[
                'saat_pelayanan'=>$q->peserta->umur->umurSaatPelayanan,
                'sekarang'=>$q->peserta->umur->umurSekarang,
            ]
        ];
        return $result;
    }
    /**
     * $type : 1=nik, 2=nokartu
     */
    function getdata($nokartu,$type)
    {
        $url[]='Peserta';
        if($type==1){
            $url[]='nik';
        }else{
            $url[]='nokartu';
        }
        $url[]=trim($nokartu);
        $url[]='tglSEP';
        $url[]=date('Y-m-d');
        if($this->setup([
            'method'=>'GET',
            'url'=>implode('/',$url)
        ])->run()){
            $this->data=$this->formatReturnPeserta($this->getResponse());
            return true;
        }else{
            return false;
        }
        return $this;
    }
}