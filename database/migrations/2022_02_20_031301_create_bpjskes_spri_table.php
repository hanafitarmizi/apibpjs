<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesSpriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_spri', function (Blueprint $table) {
            $table->increments('spri_id');
            $table->string('spri_no_surat',50)->nullable();
            $table->string('spri_pasien_kode',10);
            $table->string('spri_no_kartu',50);
            $table->date('spri_tgl_kontrol');
            $table->string('spri_bpjs_dokter_kode',10);
            $table->string('spri_bpjs_dokter_nama',255);
            $table->string('spri_simrs_dokter_kode',10)->nullable();
            $table->string('spri_bpjs_unit_kode',10);
            $table->string('spri_bpjs_unit_nama',255);
            $table->string('spri_simrs_unit_kode',10)->nullable();
            $table->timestamp('spri_created_at')->nullable();
            $table->string('spri_created_by',5)->nullable();
            $table->timestamp('spri_updated_at')->nullable();
            $table->string('spri_updated_by',5)->nullable();
            $table->timestamp('spri_deleted_at')->nullable();
            $table->string('spri_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_spri');
    }
}
