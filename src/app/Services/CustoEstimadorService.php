<?php

namespace App\Services;

use App\Models\CompraItem;
use App\Models\Embalagem;
use App\Models\Empresa;
use App\Models\Produto;
use App\Models\ReceitaPacote;
use App\Models\ReceitaTamanho;

class CustoEstimadorService
{
    public function formatarReal(float $valor): string
    {
        return 'R$ '.number_format(round($valor, 2), 2, ',', '.');
    }

    public function formatarPercentual(float $valor): string
    {
        return number_format(round($valor, 2), 2, ',', '.').'%';
    }

    public function unidadeMedidaDoProduto(mixed $produtoId): string
    {
        if (blank($produtoId)) {
            return '-';
        }

        $unidadeMedida = Produto::find($produtoId)?->unidadeMedida;

        if (! $unidadeMedida) {
            return '-';
        }

        return $unidadeMedida->sigla ?: $unidadeMedida->descricao;
    }

    /**
     * @param  iterable<array{produto_id?: mixed, quantidade?: mixed}|object>  $itens
     */
    public function calcularValorEstimado(iterable $itens): float
    {
        $total = 0.0;

        foreach ($itens as $item) {
            $produtoId = is_array($item) ? ($item['produto_id'] ?? null) : ($item->produto_id ?? null);
            $quantidade = (float) (is_array($item) ? ($item['quantidade'] ?? 0) : ($item->quantidade ?? 0));

            if (blank($produtoId) || $quantidade <= 0) {
                continue;
            }

            $total += $this->custoUltimaCompra((int) $produtoId, $quantidade);
        }

        return round($total, 2);
    }

    public function custoUltimaCompra(int $produtoId, float $quantidadeUsada): float
    {
        $ultimoItem = CompraItem::query()
            ->where('produto_id', $produtoId)
            ->join('compras', 'compras.id', '=', 'compra_itens.compra_id')
            ->orderByDesc('compras.data_compra')
            ->orderByDesc('compra_itens.id')
            ->select('compra_itens.*')
            ->first();

        if (! $ultimoItem || (float) $ultimoItem->quantidade <= 0) {
            return 0.0;
        }

        // A quantidade da compra é em "pacotes/embalagens", enquanto a
        // quantidade usada é na unidade base do produto (ex.: gramas).
        // O campo "medida" do produto informa quanto de unidade base cada
        // pacote comprado equivale (ex.: pacote de 5kg = 5000g).
        $medida = (float) (Produto::find($produtoId)?->medida ?? 0);

        if ($medida <= 0) {
            $medida = 1;
        }

        $quantidadeBaseComprada = (float) $ultimoItem->quantidade * $medida;

        if ($quantidadeBaseComprada <= 0) {
            return 0.0;
        }

        return ((float) $ultimoItem->valor_total / $quantidadeBaseComprada) * $quantidadeUsada;
    }

    public function custoUnitarioTamanho(ReceitaTamanho $tamanho): float
    {
        $tamanho->loadMissing('receita.itens');

        $totalReceita = $this->calcularValorEstimado($tamanho->receita?->itens ?? []);
        $quantidadeProduzida = (float) $tamanho->quantidade_produzida;

        if ($quantidadeProduzida <= 0) {
            return 0.0;
        }

        return $totalReceita / $quantidadeProduzida;
    }

    public function custoPacote(ReceitaPacote $pacote): float
    {
        $pacote->loadMissing(['tamanho', 'embalagem.itens']);

        // Arredonda o custo unitário do biscoito para centavos antes de
        // multiplicar pela quantidade do pacote — é assim que o valor por
        // unidade aparece na tela, então o cálculo do pacote deve bater
        // com a conta que o usuário faria de cabeça a partir dele.
        $custoUnitario = $pacote->tamanho ? round($this->custoUnitarioTamanho($pacote->tamanho), 2) : 0.0;
        $quantidadePorPacote = (float) $pacote->quantidade_por_pacote;
        $custoMateriais = $this->calcularValorEstimado($pacote->embalagem?->itens ?? collect());

        return ($custoUnitario * $quantidadePorPacote) + $custoMateriais;
    }

    /**
     * Custo de mão de obra de uma única unidade do tamanho, rateando o
     * tempo total gasto para produzir a receita (quantidade_horas) pela
     * quantidade produzida daquele tamanho especificamente.
     */
    public function custoMaoDeObraUnitario(ReceitaTamanho $tamanho): float
    {
        $tamanho->loadMissing('receita');

        $quantidadeProduzida = (float) $tamanho->quantidade_produzida;

        if ($quantidadeProduzida <= 0) {
            return 0.0;
        }

        $valorHora = (float) ($this->empresaAtual()?->valor_hora_maodeobra ?? 0);
        $horas = (float) ($tamanho->receita?->quantidade_horas ?? 0);

        return ($valorHora * $horas) / $quantidadeProduzida;
    }

    public function custoMaoDeObraPacote(ReceitaPacote $pacote): float
    {
        $pacote->loadMissing('tamanho');

        if (! $pacote->tamanho) {
            return 0.0;
        }

        return round($this->custoMaoDeObraUnitario($pacote->tamanho), 2) * (float) $pacote->quantidade_por_pacote;
    }

    public function custoEmbalagem(?int $embalagemId): float
    {
        if (blank($embalagemId)) {
            return 0.0;
        }

        return $this->calcularValorEstimado(Embalagem::find($embalagemId)?->itens ?? collect());
    }

    public function empresaAtual(): ?Empresa
    {
        $empresaId = session('empresa_id');

        return $empresaId ? Empresa::find($empresaId) : Empresa::first();
    }

    /**
     * Discrimina o preço de venda a partir do custo de produção. Lucro e
     * despesa fixa são percentuais cadastrados na empresa, aplicados sobre
     * o custo de produção (matéria-prima + embalagem). A mão de obra não é
     * mais um percentual: é calculada fora deste método (valor da hora da
     * empresa × horas da receita, rateado pela quantidade produzida) e
     * apenas somada ao valor de venda.
     *
     * @return array{custo_producao: float, valor_lucro: float, valor_mao_de_obra: float, valor_despesa_fixa: float, valor_venda: float}
     */
    public function detalharVenda(float $custoProducao, float $custoMaoDeObra = 0.0): array
    {
        $empresa = $this->empresaAtual();

        $percentualLucro = (float) ($empresa?->percentual_lucro ?? 0);
        $percentualDespesaFixa = (float) ($empresa?->percentual_despesa_fixa ?? 0);

        $custoProducao = round($custoProducao, 2);
        $valorMaoDeObra = round($custoMaoDeObra, 2);
        $valorLucro = round($custoProducao * $percentualLucro / 100, 2);
        $valorDespesaFixa = round($custoProducao * $percentualDespesaFixa / 100, 2);

        return [
            'custo_producao' => $custoProducao,
            'valor_lucro' => $valorLucro,
            'valor_mao_de_obra' => $valorMaoDeObra,
            'valor_despesa_fixa' => $valorDespesaFixa,
            'valor_venda' => round($custoProducao + $valorLucro + $valorMaoDeObra + $valorDespesaFixa, 2),
        ];
    }
}
