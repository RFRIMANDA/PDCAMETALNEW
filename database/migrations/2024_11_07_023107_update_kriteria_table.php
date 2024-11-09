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
    Schema::table('kriteria', function (Blueprint $table) {
        $table->json('desc_kriteria')->nullable()->change();
        $table->json('nilai_kriteria')->nullable()->change();
    });
}

public function down()
{
    Schema::table('kriteria', function (Blueprint $table) {
        $table->string('desc_kriteria')->change();
        $table->string('nilai_kriteria')->change();
    });
}
};
