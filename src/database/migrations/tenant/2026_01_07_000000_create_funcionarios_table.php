<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('entidade_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('apelido', 20)->nullable();
            $table->string('cargo', 60)->nullable();
            $table->string('rg', 15)->nullable();
            $table->decimal('valor_salario', 12, 2)->default(0);
            $table->boolean('bloqueado')->default(false);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('empresa_id')
                ->nullable()
                ->constrained('empresas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
