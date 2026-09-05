<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('tipo_movimento_id')
                ->nullable()
                ->after('fornecedor_id')
                ->constrained('tipos_movimento')
                ->nullOnDelete();
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->foreignId('tipo_movimento_id')
                ->nullable()
                ->after('compra_id')
                ->constrained('tipos_movimento')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_movimento_id');
        });

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_movimento_id');
        });
    }
};
