<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingKondisiPulangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_kondisi_pulang', function (Blueprint $table) {
            $table->increments('mkp_id');
            $table->string('mkp_bpjs_kondisi_pulang_kode',10);
            $table->string('mkp_simrs_kondisi_pulang_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_kondisi_pulang');
    }
}
