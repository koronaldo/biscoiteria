<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microrregioes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesorregiao_id')->constrained('mesorregioes');
            $table->string('nome');
            $table->string('codigo_ibge', 5)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microrregioes');
    }
};
