<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned()->default(11);
            $table->integer('paket_id')->unsigned()->default(11);
            $table->integer('berat');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan');
            $table->enum('status_pembayaran', array('lunas', 'belum_lunas'))->default('belum_lunas');
            $table->enum('status_cucian', array('proses', 'selesai'))->default('proses');
            $table->integer('total_harga');
            $table->timestamps();
            $table->foreign('member_id')->references('id')->on('member')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('paket_id')->references('id')->on('paket')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
