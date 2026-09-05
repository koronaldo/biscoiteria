<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A quantidade produzida passa a ser definida por tamanho (tabela
        // receita_tamanhos). Preserva o valor já cadastrado como o
        // tamanho "Padrão" antes de remover a coluna antiga.
        $receitas = DB::table('receitas')->where('quantidade_produzida', '>', 0)->get(['id', 'quantidade_produzida']);

        foreach ($receitas as $receita) {
            DB::table('receita_tamanhos')->insert([
                'receita_id' => $receita->id,
                'descricao' => 'Padrão',
                'quantidade_produzida' => $receita->quantidade_produzida,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('receitas', function (Blueprint $table) {
            $table->dropColumn('quantidade_produzida');
        });
    }

    public function down(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            $table->decimal('quantidade_produzida', 12, 2)->default(0);
        });
    }
};
