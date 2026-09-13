<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->decimal('valor_hora_maodeobra', 12, 2)->default(0)->after('percentual_maodeobra');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('percentual_maodeobra');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->decimal('percentual_maodeobra', 12, 2)->default(0);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('valor_hora_maodeobra');
        });
    }
};
