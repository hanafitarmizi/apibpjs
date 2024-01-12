<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLembarPengajuanKlaim extends Migration
{
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes.lembar_pengajuan_klaim_', function (Blueprint $table) {
            $table->increments('lpk_id');
            $table->string('lpk_no_pasien',10);
            $table->string('lpk_no_daftar',10);
            $table->string('lpk_no_sep',50);
            $table->date('lpk_tgl_masuk');
            $table->date('lpk_tgl_keluar');
            $table->smallInteger('lpk_jaminan_kode');
            $table->string('lpk_jaminan_nama',50);
            $table->string('lpk_bpjs_unit_kode',5);
            $table->string('lpk_bpjs_unit_nama',100);
            $table->string('lpk_simrs_unit_kode',5);

            $table->string('lpk_bpjs_ruang_rawat_kode',5);
            $table->string('lpk_bpjs_ruang_rawat_nama',100);
            $table->string('lpk_simrs_ruang_rawat_kode',5);

            $table->string('lpk_bpjs_kelas_rawat_kode',5);
            $table->string('lpk_bpjs_kelas_rawat_nama',100);
            $table->string('lpk_simrs_kelas_rawat_kode',5);

            $table->string('lpk_bpjs_spesialis_kode',5);
            $table->string('lpk_bpjs_spesialis_nama',100);
            $table->string('lpk_simrs_spesialis_kode',5);

            $table->string('lpk_bpjs_cara_keluar_kode',5);
            $table->string('lpk_bpjs_cara_keluar_nama',100);
            $table->string('lpk_simrs_cara_keluar_kode',5);

            $table->string('lpk_bpjs_kondisi_pulang_kode',5);
            $table->string('lpk_bpjs_kondisi_pulang_nama',100);
            $table->string('lpk_simrs_kondisi_pulang_kode',5);

            $table->string('lpk_tindak_lanjut',5);
            $table->string('lpk_ppk_kode',5);
            $table->string('lpk_ppk_nama',100);

            $table->timestamp('lpk_tgl_kontrol');

            $table->string('lpk_bpjs_unit_kontrol_kode',5);
            $table->string('lpk_bpjs_unit_kontrol_nama',100);
            $table->string('lpk_simrs_unit_kontrol_kode',5);

            $table->string('lpk_bpjs_dpjp_kode',10);
            $table->string('lpk_bpjs_dpjp_nama',100);
            $table->string('lpk_simrs_dpjp_kode',10);

            $table->timestamp('lpk_created_at');
            $table->string('lpk_created_by',5);
            $table->timestamp('lpk_updated_at');
            $table->string('lpk_updated_by',5);
            $table->timestamp('lpk_deleted_at');
            $table->string('lpk_deleted_by',5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lembar_pengajuan_klaim_');
    }
}
