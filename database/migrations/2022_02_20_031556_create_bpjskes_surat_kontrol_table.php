<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesSuratKontrolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_surat_kontrol', function (Blueprint $table) {
            $table->increments('sk_id');
            $table->string('sk_no_surat',50)->nullable();
            $table->string('sk_pasien_kode',10);
            $table->string('sk_reg_kode',10);
            $table->string('sk_no_sep',50);
            $table->date('sk_tgl_kontrol');
            $table->date('sk_tgl_kontrol_expire');
            $table->string('sk_bpjs_dokter_kode',10);
            $table->string('sk_bpjs_dokter_nama',255);
            $table->string('sk_simrs_dokter_kode',10)->nullable();
            $table->string('sk_bpjs_unit_kode',10);
            $table->string('sk_bpjs_unit_nama',255);
            $table->string('sk_simrs_unit_kode',10)->nullable();
            $table->timestamp('sk_created_at')->nullable();
            $table->string('sk_created_by',5)->nullable();
            $table->timestamp('sk_updated_at')->nullable();
            $table->string('sk_updated_by',5)->nullable();
            $table->timestamp('sk_deleted_at')->nullable();
            $table->string('sk_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_surat_kontrol');
    }
}
