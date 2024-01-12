<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingDokterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_dokter', function (Blueprint $table) {
            $table->increments('mdr_id');
            $table->string('mdr_bpjs_dokter_kode',10);
            $table->string('mdr_simrs_dokter_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_dokter');
    }
}
