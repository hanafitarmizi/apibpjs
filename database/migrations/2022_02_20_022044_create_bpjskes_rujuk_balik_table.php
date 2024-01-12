<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesRujukBalikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_rujuk_balik', function (Blueprint $table) {
            $table->increments('prb_id');
            $table->string('prb_nomor',10)->nullable();
            $table->string('rjk_pasien_kode',50)->nullable();
            $table->date('prb_tgl_srb')->nullable();
            $table->string('prb_pasien_kode',10);
            $table->string('prb_reg_kode',10);
            $table->string('prb_no_sep',50);
            $table->string('prb_no_kartu',10);
            $table->text('prb_alamat');
            $table->string('prb_email',255);
            $table->string('prb_bpjs_program_kode',10);
            $table->string('prb_bpjs_program_nama',255);
            $table->string('prb_simrs_program_kode',10)->nullable();
            $table->string('prb_bpjs_dpjp_kode',10);
            $table->string('prb_bpjs_dpjp_nama',255);
            $table->string('prb_simrs_dpjp_kode',10)->nullable();
            $table->text('prb_ket')->nullable();
            $table->text('prb_saran')->nullable();
            $table->timestamp('prb_created_at')->nullable();
            $table->string('prb_created_by',5)->nullable();
            $table->timestamp('prb_updated_at')->nullable();
            $table->string('prb_updated_by',5)->nullable();
            $table->timestamp('prb_deleted_at')->nullable();
            $table->string('prb_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_rujuk_balik');
    }
}
