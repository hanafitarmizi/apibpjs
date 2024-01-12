<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjskesSepPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjskes_sep_pengajuan', function (Blueprint $table) {
            $table->increments('psp_id');
            $table->string('psp_pasien_kode',10);
            $table->string('psp_no_kartu',50);
            $table->date('psp_tgl_sep');
            $table->tinyInteger('psp_jenis_pelayanan',1)->comment('1=ri,2=rj');
            $table->tinyInteger('psp_jenis_pengajuan',1)->comment('1. pengajuan backdate, 2. pengajuan finger print');
            $table->tinyInteger('psp_status',1)->comment('1=ri,2=rj')->nullable();
            $table->text('psp_ket_pengajuan');
            $table->text('psp_ket_approval');
            $table->timestamp('psp_approved_at')->nullable();
            $table->string('psp_approved_by',5)->nullable();
            $table->timestamp('psp_created_at')->nullable();
            $table->string('psp_created_by',5)->nullable();
            $table->timestamp('psp_updated_at')->nullable();
            $table->string('psp_updated_by',5)->nullable();
            $table->timestamp('psp_deleted_at')->nullable();
            $table->string('psp_deleted_by',5)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjskes_sep_pengajuan');
    }
}
