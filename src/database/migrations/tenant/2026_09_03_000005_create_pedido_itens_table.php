<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->cascadeOnDelete();

            $table->foreignId('receita_tamanho_id')
                ->nullable()
                ->constrained('receita_tamanhos')
                ->nullOnDelete();

            $table->string('forma_venda', 20)->default('unidade');
            $table->decimal('quantidade', 12, 2)->default(0);
            $table->decimal('preco_unitario', 12, 2)->default(0);
            $table->decimal('valor_total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_itens');
    }
};
