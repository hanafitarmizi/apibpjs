<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingPoliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_mapping_poli', function (Blueprint $table) {
            $table->increments('mpl_id');
            $table->string('mpl_bpjs_poli_kode',10);
            $table->string('mpl_simrs_poli_kode',10);
            $table->timestamp('mpl_created_at')->nullable();
            $table->string('mpl_created_by',5)->nullable();
            $table->timestamp('mpl_updated_at')->nullable();
            $table->string('mpl_updated_by',5)->nullable();
            $table->timestamp('mpl_deleted_at')->nullable();
            $table->string('mpl_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_mapping_poli');
    }
}
