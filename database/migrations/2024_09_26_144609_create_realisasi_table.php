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
        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();
            $table->string('id_tindakan');
            $table->enum('anumgoal', ['Efficiency Cost', 'Time', 'Human Resources']); // Dropdown untuk anumgoal
            $table->decimal('anumbudget', 15, 2); // Format currency (IDR) dengan maksimal 15 digit dan 2 desimal
            $table->string('realisasi');
            $table->string('responsible');
            $table->string('accountable');
            $table->string('consulted');
            $table->string('informed');
            $table->date('tgl_penyelesaian'); // Target tanggal selesai
            $table->date('tgl_realisasi'); // Tanggal realisasi untuk setiap input
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
        Schema::dropIfExists('realisasi');
    }
};
