<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embalagens', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 150);
            $table->timestamps();
        });

        Schema::create('embalagem_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('embalagem_id')
                ->constrained('embalagens')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')
                ->nullable()
                ->constrained('produtos')
                ->nullOnDelete();

            $table->decimal('quantidade', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embalagem_itens');
        Schema::dropIfExists('embalagens');
    }
};
