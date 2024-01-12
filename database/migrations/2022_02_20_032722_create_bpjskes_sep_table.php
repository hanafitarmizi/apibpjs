<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesSepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_sep', function (Blueprint $table) {
            $table->increments('sep_id');
            $table->string('sep_pasien_kode',10);
            $table->string('sep_reg_kode',10)->nullable();
            $table->string('sep_no_sep',50)->nullable();
            $table->string('sep_no_rujukan',50);
            $table->string('sep_no_kartu',50);
            $table->date('sep_tgl_rujukan')->nullable();
            $table->date('sep_tgl_sep');
            $table->date('sep_tgl_checkout_sep')->nullable();
            $table->string('sep_ppk_kode',10)->nullable()->comment('instansi pemberi pelayanan kesehatan');
            $table->string('sep_asal_rujukan_kode',10)->nullable();
            $table->string('sep_asal_rujukan_nama',255)->nullable();
            $table->tinyInteger('sep_asal_rujukan_tingkat',1)->nullable()->comment('1,2');
            $table->tinyInteger('sep_jenis_pelayanan',1)->comment('1=ri,2=rj');
            $table->tinyInteger('sep_hak_kelas',1)->comment('1. Kelas 1, 2. Kelas 2, 3. Kelas 3');
            $table->tinyInteger('sep_kelas_rawat',1)->nullable()->comment('1=ri,2=rj');
            $table->tinyInteger('sep_kelas_rawat_status',1)->nullable()->comment('1=naik kelas,0=tidak naik kelas');
            $table->tinyInteger('sep_jenis_pembiayaan',1)->nullable()->comment('1=pribadi,2=pemberi kerja,3=asuransi kesehatan tambahan. diisi jika naik kelas rawat');
            $table->string('sep_penanggung_jawab',255)->nullable()->comment('jika pembiayaan 1 maka penanggungJawab=Pribadi. diisi jika naik kelas rawat');
            $table->string('sep_poli_kode',10)->nullable();
            $table->string('sep_poli_nama',255)->nullable();
            $table->string('sep_simrs_poli_kode',10)->nullable();
            $table->string('sep_diagnosa_kode',10)->nullable();
            $table->string('sep_diagnosa_nama',255)->nullable();
            $table->string('sep_dpjp_kode',10)->nullable();
            $table->string('sep_dpjp_nama',255)->nullable();
            $table->string('sep_simrs_dpjp_kode',10)->nullable();
            $table->string('sep_skdp_no_surat',255)->nullable();
            $table->string('sep_no_telp',50);
            $table->string('sep_catatan',255);
            $table->tinyInteger('sep_is_kontrol_post_ri',1)->nullable()->comment('1=y,0=n');
            $table->tinyInteger('sep_is_poli_eksekutif',1)->nullable()->comment('1=y,0=n');
            $table->tinyInteger('sep_is_bridging',1)->nullable()->comment('1=y,0=n');
            $table->tinyInteger('sep_is_cob',1)->nullable()->comment('1=y,0=n');
            $table->tinyInteger('sep_is_katarak',1)->nullable()->comment('1=y,0=n');
            $table->char('sep_tujuan_kunjungan',1);
            $table->char('sep_flag_prosedur',1)->nullable();
            $table->char('sep_penunjang_kode',2)->nullable();
            $table->string('sep_simrs_penunjang_kode',10)->nullable();
            $table->char('sep_ases_pelayanan',1)->nullable();
            $table->tinyInteger('sep_is_duplikat',1)->nullable()->comment('1=y,0=n');
            $table->char('sep_is_laka_lantas',1)->nullable();
            $table->date('sep_laka_lantas_tgl_kejadian')->nullable();
            $table->text('sep_laka_lantas_ket')->nullable();
            $table->tinyInteger('sep_laka_lantas_suplesi',1)->nullable();
            $table->string('sep_laka_lantas_no_suplesi',255)->nullable();
            $table->string('sep_laka_lantas_prov_kode',10)->nullable();
            $table->string('sep_laka_lantas_prov_nama',255)->nullable();
            $table->string('sep_laka_lantas_kab_kode',10)->nullable();
            $table->string('sep_laka_lantas_kab_nama',255)->nullable();
            $table->string('sep_laka_lantas_kec_kode',10)->nullable();
            $table->string('sep_laka_lantas_kec_nama',255)->nullable();


            
            $table->timestamp('sep_created_at')->nullable();
            $table->string('sep_created_by',5)->nullable();
            $table->timestamp('sep_updated_at')->nullable();
            $table->string('sep_updated_by',5)->nullable();
            $table->timestamp('sep_deleted_at')->nullable();
            $table->string('sep_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_sep');
    }
}
