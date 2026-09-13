<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\CustoEstimadorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PedidoImpressaoController extends Controller
{
    public function show(Pedido $pedido, CustoEstimadorService $custo): Response
    {
        $pedido->load([
            'cliente.entidade',
            'itens.receitaTamanho.receita',
            'itens.receitaPacote.receita',
        ]);

        $empresa = $custo->empresaAtual()?->loadMissing(['entidade.telefones', 'entidade.emails']);

        $pdf = Pdf::loadView('pedidos.imprimir', [
            'pedido' => $pedido,
            'empresa' => $empresa,
            'custo' => $custo,
        ])->setPaper('a4');

        return $pdf->stream("orcamento-pedido-{$pedido->id}.pdf");
    }
}
