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
        Schema::create('resiko', function (Blueprint $table) {
            $table->id();
            $table->string('id_riskregister')->nullable();
            $table->string('nama_resiko')->nullable();
            $table->enum('kriteria', [
                'Unsur keuangan / Kerugian',
                'Safety & Health',
                'Enviromental (lingkungan)'
            ])->nullable();
            $table->unsignedInteger('probability')->nullable(); // nullable
            $table->unsignedInteger('severity')->nullable(); // nullable
            $table->enum('tingkatan', [
                'LOW',
                'MODERATE',
                'HIGH',
                'EXTREME'
            ])->nullable(); // nullable
            $table->enum('status', [
                'OPEN',
                'ON PROGRES',
                'CLOSE'
            ])->nullable(); // nullable
            $table->string('risk')->nullable();
            $table->string('target')->nullable();
            $table->string('before')->nullable();
            $table->string('after')->nullable();
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
        Schema::dropIfExists('resiko');
    }
};
