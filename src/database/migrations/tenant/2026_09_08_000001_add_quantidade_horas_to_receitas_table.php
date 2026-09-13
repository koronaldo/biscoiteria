<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            $table->decimal('quantidade_horas', 12, 2)->default(0)->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            $table->dropColumn('quantidade_horas');
        });
    }
};
