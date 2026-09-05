<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receita_tamanhos', function (Blueprint $table) {
            $table->decimal('quantidade_por_pacote', 12, 2)->default(0)->after('quantidade_produzida');
        });
    }

    public function down(): void
    {
        Schema::table('receita_tamanhos', function (Blueprint $table) {
            $table->dropColumn('quantidade_por_pacote');
        });
    }
};
