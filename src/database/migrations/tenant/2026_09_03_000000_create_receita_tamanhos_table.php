<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receita_tamanhos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receita_id')
                ->constrained('receitas')
                ->cascadeOnDelete();

            $table->string('descricao', 100);
            $table->decimal('quantidade_produzida', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receita_tamanhos');
    }
};
