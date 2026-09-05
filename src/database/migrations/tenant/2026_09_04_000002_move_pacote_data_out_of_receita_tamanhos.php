<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O "pacote" deixou de ser um campo dentro do tamanho e virou um
        // cadastro próprio (um tamanho pode ter vários pacotes diferentes).
        // Preserva o que já foi cadastrado antes de remover as colunas antigas.
        $tamanhos = DB::table('receita_tamanhos')
            ->where('quantidade_por_pacote', '>', 0)
            ->get(['id', 'receita_id', 'descricao', 'quantidade_por_pacote']);

        foreach ($tamanhos as $tamanho) {
            $pacoteId = DB::table('receita_pacotes')->insertGetId([
                'receita_id' => $tamanho->receita_id,
                'receita_tamanho_id' => $tamanho->id,
                'descricao' => $tamanho->descricao,
                'quantidade_por_pacote' => $tamanho->quantidade_por_pacote,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $embalagens = DB::table('receita_tamanho_embalagens')
                ->where('receita_tamanho_id', $tamanho->id)
                ->get(['produto_id', 'quantidade']);

            foreach ($embalagens as $embalagem) {
                DB::table('receita_pacote_embalagens')->insert([
                    'receita_pacote_id' => $pacoteId,
                    'produto_id' => $embalagem->produto_id,
                    'quantidade' => $embalagem->quantidade,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::dropIfExists('receita_tamanho_embalagens');

        Schema::table('receita_tamanhos', function (Blueprint $table) {
            $table->dropColumn('quantidade_por_pacote');
        });

        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->foreignId('receita_pacote_id')
                ->nullable()
                ->after('receita_tamanho_id')
                ->constrained('receita_pacotes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedido_itens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('receita_pacote_id');
        });

        Schema::table('receita_tamanhos', function (Blueprint $table) {
            $table->decimal('quantidade_por_pacote', 12, 2)->default(0);
        });

        Schema::create('receita_tamanho_embalagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receita_tamanho_id')
                ->constrained('receita_tamanhos')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')
                ->nullable()
                ->constrained('produtos')
                ->nullOnDelete();

            $table->decimal('quantidade', 12, 2)->default(0);

            $table->timestamps();
        });
    }
};
