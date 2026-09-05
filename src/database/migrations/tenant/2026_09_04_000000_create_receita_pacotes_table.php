<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receita_pacotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receita_id')
                ->constrained('receitas')
                ->cascadeOnDelete();

            $table->foreignId('receita_tamanho_id')
                ->nullable()
                ->constrained('receita_tamanhos')
                ->nullOnDelete();

            $table->string('descricao', 100);
            $table->decimal('quantidade_por_pacote', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receita_pacotes');
    }
};
