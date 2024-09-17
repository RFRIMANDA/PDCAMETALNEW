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
        Schema::create('listkecil', function (Blueprint $table) {
            $table->id();  // Primary key dan auto increment secara default
            $table->string('id_listform')->nullable();
            $table->string('realisasi')->nullable();
            $table->string('responsible')->nullable();
            $table->string('accountable')->nullable();
            $table->string('consulted')->nullable();
            $table->string('informed')->nullable();
            $table->string('anumgoal')->nullable();
            $table->string('anumbudget')->nullable();
            $table->string('desc')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('listkecil');
    }
};
