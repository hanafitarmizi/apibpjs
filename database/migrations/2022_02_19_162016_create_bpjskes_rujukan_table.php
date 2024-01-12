<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesRujukanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_rujukan', function (Blueprint $table) {
            $table->increments('ruj_id');
            $table->string('ruj_pasien_kode',3);
            $table->string('ruj_reg_kode',10)->nullable();
            $table->string('ruj_no_rujukan',50);
            $table->string('ruj_no_sep',50);
            $table->string('ruj_nama',255);
            $table->string('ruj_no_kartu',50);
            $table->char('ruj_jkel',1);
            $table->date('ruj_tgl_rujukan');
            $table->date('ruj_tgl_kunjungan');
            $table->tinyInteger('ruj_ppk_dirujuk_tingkat',1);
            $table->string('ruj_ppk_dirujuk_kode',10);
            $table->string('ruj_ppk_dirujuk_nama',255);
            $table->char('ruj_jenis_pelayanan',1)->comment('1=ri, 2=rj');
            $table->string('ruj_diagnosa_kode',5);
            $table->string('ruj_diagnosa_nama',255);
            $table->string('ruj_simrs_diagnosa_kode',10)->nullable();
            $table->string('ruj_poli_kode',10);
            $table->string('ruj_poli_nama',255);
            $table->string('ruj_simrs_poli_kode',10)->nullable();
            $table->tinyInteger('ruj_tipe_rujukan',1)->comment('0=rujukan penuh,1=partial,2=balik');
            $table->tinyInteger('ruj_is_bridging',1)->comment('1=y,0=n')->default(1);
            $table->text('ruj_catatan');
            $table->timestamp('ruj_created_at')->nullable();
            $table->string('ruj_created_by',5)->nullable();
            $table->timestamp('ruj_updated_at')->nullable();
            $table->string('ruj_updated_by',5)->nullable();
            $table->timestamp('ruj_deleted_at')->nullable();
            $table->string('ruj_deleted_by',5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_rujukan');
    }
}
