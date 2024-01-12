<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesRujukanKhususTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_rujukan_khusus', function (Blueprint $table) {
            $table->increments('rjk_id');
            $table->string('rjk_pasien_kode',10);
            $table->string('rjk_reg_kode',10);
            $table->string('rjk_no_rujukan',50);
            $table->string('rjk_bpjs_diagnosa_primer_kode',10);
            $table->string('rjk_bpjs_diagnosa_primer_nama',255);
            $table->string('rjk_simrs_diagnosa_primer_kode',10)->nullable();
            $table->string('rjk_bpjs_diagnosa_sekunder_kode',10);
            $table->string('rjk_bpjs_diagnosa_sekunder_nama',255);
            $table->string('rjk_simrs_diagnosa_sekunder_kode',10)->nullable();
            $table->string('rjk_bpjs_prosedur_kode',10);
            $table->string('rjk_bpjs_prosedur_nama',255);
            $table->string('rjk_simrs_prosedur_kode',10)->nullable();
            $table->date('rjk_tgl_awal_rujukan')->nullable();
            $table->date('rjk_tgl_akhir_rujukan')->nullable();
            $table->timestamp('rjk_created_at')->nullable();
            $table->string('rjk_created_by',5)->nullable();
            $table->timestamp('rjk_updated_at')->nullable();
            $table->string('rjk_updated_by',5)->nullable();
            $table->timestamp('rjk_deleted_at')->nullable();
            $table->string('rjk_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_rujukan_khusus');
    }
}
