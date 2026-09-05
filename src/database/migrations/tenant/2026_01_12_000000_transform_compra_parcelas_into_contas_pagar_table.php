<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('compra_parcelas', 'contas_pagar');

        DB::statement('ALTER TABLE contas_pagar DROP CONSTRAINT compra_parcelas_compra_id_foreign');
        DB::statement('ALTER TABLE contas_pagar ALTER COLUMN compra_id DROP NOT NULL');
        DB::statement('ALTER TABLE contas_pagar ALTER COLUMN numero_parcela DROP NOT NULL');
        DB::statement('ALTER TABLE contas_pagar RENAME COLUMN valor_parcela TO valor');

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->foreign('compra_id')->references('id')->on('compras')->nullOnDelete();

            $table->foreignId('fornecedor_id')
                ->nullable()
                ->after('compra_id')
                ->constrained('fornecedores')
                ->nullOnDelete();

            $table->string('descricao')->nullable()->after('fornecedor_id');

            $table->boolean('pago')->default(false)->after('valor');
            $table->date('data_pagamento')->nullable()->after('pago');
            $table->decimal('valor_pago', 12, 2)->nullable()->after('data_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fornecedor_id');
            $table->dropColumn(['descricao', 'pago', 'data_pagamento', 'valor_pago']);
            $table->dropForeign(['compra_id']);
        });

        DB::statement('ALTER TABLE contas_pagar RENAME COLUMN valor TO valor_parcela');
        DB::statement('ALTER TABLE contas_pagar ALTER COLUMN numero_parcela SET NOT NULL');
        DB::statement('DELETE FROM contas_pagar WHERE compra_id IS NULL');
        DB::statement('ALTER TABLE contas_pagar ALTER COLUMN compra_id SET NOT NULL');

        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->foreign('compra_id')->references('id')->on('compras')->cascadeOnDelete();
        });

        Schema::rename('contas_pagar', 'compra_parcelas');
    }
};
