<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receita_pacotes', function (Blueprint $table) {
            $table->foreignId('embalagem_id')
                ->nullable()
                ->after('receita_tamanho_id')
                ->constrained('embalagens')
                ->nullOnDelete();
        });

        // Cada pacote tinha sua própria lista de materiais de embalagem
        // (receita_pacote_embalagens). Isso virou um cadastro reutilizável
        // (embalagens/embalagem_itens). Para não perder o que já foi
        // cadastrado, cada pacote que já tiver materiais ganha uma nova
        // Embalagem própria com os mesmos itens — sem tentar deduplicar
        // entre pacotes, o usuário poderá consolidar manualmente depois.
        $pacotes = DB::table('receita_pacotes')
            ->select('id', 'descricao')
            ->whereIn('id', function ($query) {
                $query->select('receita_pacote_id')
                    ->from('receita_pacote_embalagens')
                    ->distinct();
            })
            ->get();

        foreach ($pacotes as $pacote) {
            $embalagemId = DB::table('embalagens')->insertGetId([
                'descricao' => 'Embalagem - '.$pacote->descricao,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itens = DB::table('receita_pacote_embalagens')
                ->where('receita_pacote_id', $pacote->id)
                ->get(['produto_id', 'quantidade']);

            foreach ($itens as $item) {
                DB::table('embalagem_itens')->insert([
                    'embalagem_id' => $embalagemId,
                    'produto_id' => $item->produto_id,
                    'quantidade' => $item->quantidade,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('receita_pacotes')
                ->where('id', $pacote->id)
                ->update(['embalagem_id' => $embalagemId]);
        }

        Schema::dropIfExists('receita_pacote_embalagens');
    }

    public function down(): void
    {
        Schema::create('receita_pacote_embalagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receita_pacote_id')
                ->constrained('receita_pacotes')
                ->cascadeOnDelete();

            $table->foreignId('produto_id')
                ->nullable()
                ->constrained('produtos')
                ->nullOnDelete();

            $table->decimal('quantidade', 12, 2)->default(0);

            $table->timestamps();
        });

        Schema::table('receita_pacotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('embalagem_id');
        });
    }
};
