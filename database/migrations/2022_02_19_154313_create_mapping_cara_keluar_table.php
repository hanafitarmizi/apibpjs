<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingCaraKeluarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_cara_keluar', function (Blueprint $table) {
            $table->increments('mck_id');
            $table->string('mck_bpjs_cara_keluar_kode',10);
            $table->string('mck_simrs_cara_keluar_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_cara_keluar');
    }
}
