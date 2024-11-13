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
        Schema::create('formppk3', function (Blueprint $table) {
            $table->id();
            $table->string('id_formppk')->nullable();
            $table->string('penanggulangan')->nullable();
            $table->string('pencegahan')->nullable();
            $table->date('tgl_usulan')->nullable();
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
        Schema::dropIfExists('formppk3');
    }
};
