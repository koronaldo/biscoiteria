<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Orçamento #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            padding-bottom: 40px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .cabecalho-tabela {
            border-bottom: 2px solid #1f2937;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .cabecalho-tabela td {
            vertical-align: middle;
        }

        .cabecalho-tabela img {
            max-height: 70px;
            max-width: 200px;
        }

        .empresa-nome {
            font-size: 16px;
            font-weight: bold;
        }

        .empresa-documento {
            color: #6b7280;
        }

        .titulo {
            text-align: right;
        }

        .titulo h1 {
            margin: 0;
            font-size: 20px;
        }

        .titulo p {
            margin: 4px 0 0;
            color: #6b7280;
        }

        .dados-pedido {
            margin-bottom: 16px;
        }

        .dados-pedido td {
            padding-bottom: 12px;
        }

        .dados-pedido strong {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .itens thead th {
            text-align: left;
            border-bottom: 2px solid #1f2937;
            padding: 6px;
            font-size: 10px;
            text-transform: uppercase;
            color: #374151;
        }

        .itens tbody td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totais {
            width: 260px;
            margin-top: 12px;
            margin-left: 340px;
        }

        .totais td {
            padding: 4px 0;
        }

        .totais .total-final td {
            border-top: 2px solid #1f2937;
            font-weight: bold;
            font-size: 14px;
            padding-top: 8px;
        }

        .rodape {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }

        .rodape .rodape-empresa {
            font-weight: bold;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <table class="cabecalho-tabela">
        <tr>
            <td width="70">
                @if ($empresa?->logo)
                    <img src="data:image/png;base64,{{ $empresa->logo }}" alt="Logo">
                @endif
            </td>
            <td>
                <div class="empresa-nome">{{ $empresa?->entidade?->nome ?? 'Empresa' }}</div>
                @if ($empresa?->entidade?->documento)
                    <div class="empresa-documento">{{ $empresa->entidade->documento }}</div>
                @endif
            </td>
            <td class="titulo">
                <h1>Orçamento</h1>
                <p>Pedido nº {{ $pedido->id }}</p>
            </td>
        </tr>
    </table>

    <table class="dados-pedido">
        <tr>
            <td width="50%">
                <strong>Cliente</strong>
                {{ $pedido->cliente?->entidade?->nome ?? '-' }}
            </td>
            <td width="50%">
                <strong>Data do Pedido</strong>
                {{ $pedido->data_pedido?->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <table class="itens">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Forma de Venda</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Preço Unitário</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->itens as $item)
                <tr>
                    <td>{{ $item->descricao_item }}</td>
                    <td class="text-center">{{ $item->forma_venda === 'pacote' ? 'Pacote' : 'Unidade' }}</td>
                    <td class="text-right">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                    <td class="text-right">{{ $custo->formatarReal((float) $item->preco_unitario) }}</td>
                    <td class="text-right">{{ $custo->formatarReal((float) $item->valor_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totais">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ $custo->formatarReal($pedido->itens->sum(fn ($item) => (float) $item->valor_total)) }}</td>
        </tr>

        @if ((float) $pedido->desconto > 0)
            <tr>
                <td>Desconto</td>
                <td class="text-right">- {{ $custo->formatarReal((float) $pedido->desconto) }}</td>
            </tr>
        @endif

        <tr class="total-final">
            <td>Total</td>
            <td class="text-right">{{ $custo->formatarReal((float) $pedido->valor_total) }}</td>
        </tr>
    </table>

    @php
        $telefone = $empresa?->entidade?->telefones->firstWhere('principal', true) ?? $empresa?->entidade?->telefones->first();
        $email = $empresa?->entidade?->emails->firstWhere('principal', true) ?? $empresa?->entidade?->emails->first();
    @endphp

    <div class="rodape">
        <div class="rodape-empresa">{{ $empresa?->entidade?->nome ?? 'Empresa' }}</div>

        @if ($telefone || $email)
            <div>
                {{ $telefone?->numero }}
                @if ($telefone && $email)
                    &nbsp;&middot;&nbsp;
                @endif
                {{ $email?->email }}
            </div>
        @endif

        <div>Orçamento gerado em {{ now()->format('d/m/Y H:i') }}</div>
    </div>

</body>
</html>
