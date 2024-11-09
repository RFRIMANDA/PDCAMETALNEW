<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('id_resiko')->nullable(); // Menjadikan nullable
            $table->string('nama_kriteria')->nullable();
            $table->text('desc_kriteria')->nullable(); // Menjadikan nullable dan mengubah tipe data menjadi text untuk menampung lebih banyak data
            $table->string('nilai_kriteria')->nullable(); // Menjadikan nullable
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
        Schema::dropIfExists('kriteria');
    }
};
