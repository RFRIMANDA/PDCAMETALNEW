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
        Schema::create('formppk', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('jenisketidaksesuaian', ['Sistem', 'Proses', 'Produk', 'Audit']);
            $table->string('pembuat');
            $table->string('emailpembuat');
            $table->string('divisipembuat');
            $table->string('penerima');
            $table->string('emailpenerima');
            $table->string('divisipenerima');
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
        Schema::dropIfExists('formppk');
    }
};
