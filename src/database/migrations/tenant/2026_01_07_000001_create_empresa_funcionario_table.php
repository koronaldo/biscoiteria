<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_funcionario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['empresa_id', 'funcionario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_funcionario');
    }
};
