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
    Schema::table('formppk', function (Blueprint $table) {
        $table->string('nomor_surat')->unique()->nullable(); // Kolom nomor_surat
    });
}

public function down()
{
    Schema::table('formppk', function (Blueprint $table) {
        $table->dropColumn('nomor_surat');
    });
}
};
