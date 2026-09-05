<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telefones', function (Blueprint $table) {
            $table->id();

            $table->morphs('telefonavel');

            $table->string('numero', 20);
            $table->string('tipo', 20)->default('principal');

            $table->boolean('principal')->default(false);
            $table->boolean('whatsapp')->default(false);

            $table->timestamps();

            $table->index('numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telefones');
    }
};
