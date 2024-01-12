<?php
namespace App\Models\Bpjskes\Antrol\V2;
class Dashboard extends BaseAntrol
{
    function rekapTanggal($req)
    {
        if($this->setup([
            'url'=>'dashboard/waktutunggu/tanggal/'.date('Y-m-d',strtotime($req['tanggal'])).'/waktu/'.trim($req['jenis_waktu']),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponseDashboard();
            foreach($tmp['list'] as $q){
                $this->data[]=[
                    'ppk'=>[
                        'id'=>$q->kdppk.'!#!'.$q->nmppk,
                        'nama'=>$q->nmppk,
                    ],
                    'poliklinik'=>[
                        'id'=>$q->kodepoli.'!#!'.$q->namapoli,
                        'nama'=>$q->namapoli
                    ],
                    'tanggal'=>date('d-m-Y',strtotime($q->tanggal)),
                    'jml_antrian'=>$q->jumlah_antrean,
                    'task1_waktu'=>$q->waktu_task1,
                    'task1_avg_waktu'=>$q->avg_waktu_task1,
                    'task2_waktu'=>$q->waktu_task2,
                    'task2_avg_waktu'=>$q->avg_waktu_task2,
                    'task3_waktu'=>$q->waktu_task3,
                    'task3_avg_waktu'=>$q->avg_waktu_task3,
                    'task4_waktu'=>$q->waktu_task4,
                    'task4_avg_waktu'=>$q->avg_waktu_task4,
                    'task5_waktu'=>$q->waktu_task5,
                    'task5_avg_waktu'=>$q->avg_waktu_task5,
                    'task6_waktu'=>$q->waktu_task6,
                    'task6_avg_waktu'=>$q->avg_waktu_task6,
                ];
            }
            return true;
        }else{
            return false;
        }
    }
    function rekapBulan($req)
    {
        if($this->setup([
            'url'=>'dashboard/waktutunggu/bulan/'.trim($req['bulan']).'/tahun/'.trim($req['tahun']).'/waktu/rs',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponseDashboard();
            foreach($tmp['list'] as $q){
                $this->data[]=[
                    'ppk'=>[
                        'id'=>$q->kdppk.'!#!'.$q->nmppk,
                        'nama'=>$q->nmppk,
                    ],
                    'poliklinik'=>[
                        'id'=>$q->kodepoli.'!#!'.$q->namapoli,
                        'nama'=>$q->namapoli
                    ],
                    'tanggal'=>date('d-m-Y',strtotime($q->tanggal)),
                    'jml_antrian'=>$q->jumlah_antrean,
                    'task1_waktu'=>$q->waktu_task1,
                    'task1_avg_waktu'=>$q->avg_waktu_task1,
                    'task2_waktu'=>$q->waktu_task2,
                    'task2_avg_waktu'=>$q->avg_waktu_task2,
                    'task3_waktu'=>$q->waktu_task3,
                    'task3_avg_waktu'=>$q->avg_waktu_task3,
                    'task4_waktu'=>$q->waktu_task4,
                    'task4_avg_waktu'=>$q->avg_waktu_task4,
                    'task5_waktu'=>$q->waktu_task5,
                    'task5_avg_waktu'=>$q->avg_waktu_task5,
                    'task6_waktu'=>$q->waktu_task6,
                    'task6_avg_waktu'=>$q->avg_waktu_task6,
                ];
            }
            return true;
        }
        return false;
    }
}