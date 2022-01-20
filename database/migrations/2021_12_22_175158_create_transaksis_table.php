<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void 
     */ 
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('iuran_id');
            $table->integer('date');
            $table->integer('month');
            $table->integer('year');
            $table->integer('nominal');
            $table->string('keterangan');
            $table->integer('kategori');
            $table->string('in_or_out');
            $table->integer('is_delete');
            $table->integer('is_confirm_for_iuran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
