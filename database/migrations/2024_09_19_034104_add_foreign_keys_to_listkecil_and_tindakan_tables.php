<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToListKecilAndTindakanTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listkecil', function (Blueprint $table) {
            $table->foreign('id_listform')->references('id')->on('listform')->onDelete('cascade');
        });

        Schema::table('tindakan', function (Blueprint $table) {
            $table->foreign('id_listform')->references('id')->on('listform')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listkecil', function (Blueprint $table) {
            $table->dropForeign(['id_listform']);
        });

        Schema::table('tindakan', function (Blueprint $table) {
            $table->dropForeign(['id_listform']);
        });
    }
}
