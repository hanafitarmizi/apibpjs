<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingKelasRawatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_kelas_rawat', function (Blueprint $table) {
            $table->increments('mkr_id');
            $table->string('mkr_bpjs_kelas_rawat_kode',10);
            $table->string('mkr_simrs_kelas_rawat_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_kelas_rawat');
    }
}
