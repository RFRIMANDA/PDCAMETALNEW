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
        Schema::create('listform', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_divisi')->constrained('divisi'); // Menambahkan foreign key constraint
            $table->string('issue');
            $table->string('resiko');
            $table->string('peluang');
            $table->string('tingkatan');
            $table->string('tindakan');
            $table->string('pic');
            $table->string('status');
            $table->string('risk');
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
        Schema::dropIfExists('listform');
    }
};
