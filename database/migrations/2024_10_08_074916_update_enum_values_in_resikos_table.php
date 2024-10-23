<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumValuesInResikosTable extends Migration
{
    public function up()
    {
        // Periksa apakah table resikos ada
        if (Schema::hasTable('resikos')) {
            Schema::table('resikos', function (Blueprint $table) {
                // Ubah kolom tingkatan dengan enum yang baru
                $table->enum('tingkatan', ['LOW', 'MEDIUM', 'HIGH', 'EXTREME'])
                      ->default('LOW')
                      ->change();
            });
        }
    }

    public function down()
    {
        // Rollback ke nilai enum sebelumnya
        Schema::table('resikos', function (Blueprint $table) {
            $table->enum('tingkatan', ['LOW', 'MODERATE', 'HIGH', 'EXTREME'])
                  ->default('LOW')
                  ->change();
        });
    }
}

