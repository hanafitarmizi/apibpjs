<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingDiagnosaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_diagnosa', function (Blueprint $table) {
            $table->increments('md_id');
            $table->string('md_bpjs_diagnosa_kode',10);
            $table->string('md_simrs_diagnosa_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_diagnosa');
    }
}
