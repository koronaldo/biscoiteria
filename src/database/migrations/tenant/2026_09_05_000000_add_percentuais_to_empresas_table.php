<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->decimal('percentual_lucro', 12, 2)->default(0);
            $table->decimal('percentual_maodeobra', 12, 2)->default(0);
            $table->decimal('percentual_despesa_fixa', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['percentual_lucro', 'percentual_maodeobra', 'percentual_despesa_fixa']);
        });
    }
};
