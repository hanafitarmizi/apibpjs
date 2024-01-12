<?php
use Illuminate\Support\Facades\Cache;
$router->group(['prefix' => 'simrs'], function() use ($router){
    $router->group(['namespace' => 'Simrs'], function() use ($router){
        $router->get('push-antrol','MainController@pushAntrol');
    });
});

$router->group(['prefix' => 'bpjskes'], function() use ($router){

    //APLICARE
    $router->group(['prefix' => 'aplicare/v2'], function() use ($router){
        $router->group(['namespace' => 'Bpjskes\Aplicare\V2'], function() use ($router){

            $router->group(['prefix' => 'referensi'], function() use ($router){
                $router->post('kelas','ReferensiController@getKelasRuang');
            });

            $router->group(['prefix' => 'ruang'], function() use ($router){
                $router->post('list','RuangController@listRuang');
                $router->post('save','RuangController@saveRuang');
                $router->post('update','RuangController@updateRuang');
                $router->post('delete','RuangController@deleteRuang');
            });


        });
    });

    //ANTROL
    $router->group(['prefix' => 'antrol/v2'], function() use ($router){
        $router->group(['namespace' => 'Bpjskes\Antrol\V2'], function() use ($router){

            $router->group(['prefix' => 'referensi'], function() use ($router){
                $router->post('poliklinik','ReferensiController@getPoliklinik');
                $router->post('dokter','ReferensiController@getDokter');
                $router->post('jadwal-dokter','ReferensiController@getJadwalDokter');
                $router->post('update-jadwal-dokter','ReferensiController@updateJadwalDokter');
            });

            $router->group(['prefix' => 'antrian'], function() use ($router){
                $router->post('save','AntrianController@save');
                $router->post('update-waktu','AntrianController@updateWaktu');
                $router->post('batal','AntrianController@batalAntrian');
                $router->post('task-list','AntrianController@taskList');
                $router->post('rekap-tanggal','DashboardController@rekapitulasiTanggal');
                $router->post('rekap-bulan','DashboardController@rekapitulasiBulan');
            });
        });
    });

    //VCLAIM
    $router->group(['prefix' => 'vclaim/v2'], function() use ($router){
        $router->group(['namespace' => 'Bpjskes\Vclaim\V2'], function() use ($router){
            $router->post('peserta','PesertaController@getpeserta');
            $router->get('peserta/all','PesertaController@allpeserta');
            $router->get('', function () use ($router) {
                return "HI ini Vclaim";
            });

            $router->get('/peserta/abc', function () use ($router) {
                Cache::flush();


            });

            $router->group(['prefix' => 'monitoring'], function() use ($router){
                $router->post('kunjungan','MonitoringController@kunjungan');
                $router->post('klaim','MonitoringController@klaim');
                $router->post('histori-pelayanan','MonitoringController@historiPelayanan');
                $router->post('jaminan-jasaraharja','MonitoringController@klaimJasaRaharja');
            });

            $router->post('lpk-data','PengajuanKlaimController@getData');
            $router->post('lpk-save','PengajuanKlaimController@save');
            $router->post('lpk-update','PengajuanKlaimController@update');
            $router->post('lpk-delete','PengajuanKlaimController@delete');

            $router->group(['prefix' => 'prb'], function() use ($router){
                $router->post('search-nomor','RujukBalikController@searchByNomor');
                $router->post('search-tanggal','RujukBalikController@searchByTgl');
                $router->post('save','RujukBalikController@save');
                $router->post('update','RujukBalikController@update');
                $router->post('delete','RujukBalikController@delete');
            });

            $router->group(['prefix' => 'referensi'], function() use ($router){
                $router->post('diagnosa','ReferensiController@getDiagnosa');
                $router->post('poliklinik','ReferensiController@getPoliklinik');
                $router->post('faskes','ReferensiController@getFaskes');
                $router->post('dpjp','ReferensiController@getDpjp');
                $router->post('provinsi','ReferensiController@getProvinsi');
                $router->post('kabupaten','ReferensiController@getKabupaten');
                $router->post('kecamatan','ReferensiController@getKecamatan');
                $router->post('diagnosa-prb','ReferensiController@getDiagnosaPrb');
                $router->post('obat-generik-prb','ReferensiController@getGenerikPrb');
                $router->post('tindakan','ReferensiController@getTindakan');
                $router->post('kelas-rawat','ReferensiController@getKelasRawat');
                $router->post('dokter','ReferensiController@getDokter');
                $router->post('spesialistik','ReferensiController@getSpesialistik');
                $router->post('ruang-rawat','ReferensiController@getRuangRawat');
                $router->post('cara-keluar','ReferensiController@getCaraKeluar');
                $router->post('pasca-pulang','ReferensiController@getPascaPulang');
                $router->post('tujuan-kunjungan','ReferensiController@getTujuanKunjungan');
                $router->post('flag-prosedur','ReferensiController@getFlagProsedur');
                $router->post('pembiayaan','ReferensiController@getPembiayaan');
                $router->post('penunjang','ReferensiController@getPenunjang');
                $router->post('asesmen-pelayanan','ReferensiController@getAsesmenPelayanan');
                $router->post('kk-status','ReferensiController@getStatusKecelakaanKerja');
                $router->post('status-pulang-rawatinap','ReferensiController@getStatusPulangRawatinap');
            });

            $router->group(['prefix' => 'kontrol'], function() use($router){
                $router->post('save','KontrolController@save');
                $router->post('update','KontrolController@update');
                $router->post('delete','KontrolController@delete');
                $router->post('search-sep','KontrolController@searchSep');
                $router->post('search-kontrol','KontrolController@searchKontrol');
                $router->post('list-kontrol','KontrolController@listKontrol');
                $router->post('list-kontrol-kartu','KontrolController@listKontrolByKartu');
                $router->post('poliklinik','KontrolController@listPoliklinik');
                $router->post('dokter','KontrolController@listDokter');
            });

            $router->group(['prefix' => 'spri'], function() use($router){
                $router->post('save','SpriController@save');
                $router->post('update','SpriController@update');
            });

            $router->group(['prefix' => 'rujukan'], function() use($router){
                $router->post('search','RujukanController@searchRujukan');
                $router->post('save','RujukanController@save');
                $router->post('update','RujukanController@update');
                $router->post('delete','RujukanController@delete');
                $router->post('rujukan-keluar','RujukanController@rujukanKeluar');
                $router->post('list-spesialis','RujukanController@listSpesialis');
                $router->post('list-sarana','RujukanController@listSarana');
            });

            $router->group(['prefix' => 'rujukan-khusus'], function() use($router){
                $router->post('list','RujukanKhususController@list');
                $router->post('save','RujukanKhususController@save');
                $router->post('delete','RujukanKhususController@delete');
            });

            $router->group(['prefix' => 'sep'], function() use($router){
                $router->post('search','SepController@search');
                $router->post('save','SepController@save');
                $router->post('update','SepController@update');
                $router->post('delete','SepController@delete');
                $router->post('update-sep-pulang','SepController@updateSepPulang');
                $router->post('pengajuan','SepController@pengajuanSep');
                $router->post('approval','SepController@approvalPengajuanSep');
                $router->post('internal','SepController@listSepInternal');
                $router->post('delete-internal','SepController@deleteSepInternal');
                $router->post('search-sep-igd','SepController@getSepIgd');
                $router->post('search-post-rawatinap','SepController@searchPostRawatinap');
            });

            $router->group(['prefix' => 'jasa-raharja'], function() use($router){
                $router->post('suplesi','JasaRaharjaController@suplesi');
                $router->post('data-induk-kecelakaan','JasaRaharjaController@dataIndukKecelakaan');
            });

            $router->group(['prefix' => 'finger'], function() use($router){
                $router->post('data','FingerPrintController@getData');
                $router->post('list','FingerPrintController@getList');
            });

            $router->group(['prefix' => 'inacbg'], function() use($router){
                $router->post('data','InacbgController@getData');
            });
        });
    });
});


$router->get('/', function () use ($router) {
    return "HI";
});
