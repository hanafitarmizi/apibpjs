<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesRujukBalikObatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_rujuk_balik_obat', function (Blueprint $table) {
            $table->increments('rbo_id');
            $table->integer('rbo_rujuk_balik_id');
            $table->string('rbo_bpjs_obat_kode',50);
            $table->string('rbo_bpjs_obat_nama',255);
            $table->string('rbo_simrs_obat_kode',10);
            $table->string('rbo_signa1',10);
            $table->string('rbo_signa2',10);
            $table->integer('rbo_jml_obat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_rujuk_balik_obat');
    }
}
