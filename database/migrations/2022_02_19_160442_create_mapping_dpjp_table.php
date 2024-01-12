<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingDpjpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_dpjp', function (Blueprint $table) {
            $table->increments('mdp_id');
            $table->string('mdp_bpjs_dpjp_kode',10);
            $table->string('mdp_simrs_dpjp_kode',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_dpjp');
    }
}
