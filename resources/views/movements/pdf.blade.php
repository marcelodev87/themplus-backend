<!DOCTYPE html>
<html>

<head>
    <title>Movimentações PDF {{ str_replace('-', '/', $date) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.4;
            background-color: #fff;
            padding: 10px;
            font-size: 0.9em;
        }

        h1 {
            text-align: center;
            color: #660000;
            font-size: 1.3em;
        }

        h3 {
            color: #2e2e2e;
            border-bottom: 2px solid #660000;
            padding-bottom: 5px;
            font-size: 1.1em;
        }

        p {
            font-size: 0.9em;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 0.9em;
        }

        th {
            background-color: #660000;
            color: white;
            font-size: 1.1em;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .entrada {
            color: green;
            font-weight: bold;
        }

        .saida {
            color: red;
            font-weight: bold;
        }

        .no-records {
            font-style: italic;
            color: #888;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.2em;
            color: #660000;
            /* Alterado */
            margin-bottom: 10px;
        }

        .card-content {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h1>Detalhes de movimentações {{ str_replace('-', '/', $date) }}</h1>

    <?php
$totalEntrada = 0;
$totalSaida = 0;

foreach ($movements as $movement) {
    if ($movement['type'] === 'entrada') {
        $totalEntrada += $movement['value'];
    } else {
        $totalSaida += $movement['value'];
    }
}

$saldo = $totalEntrada - $totalSaida;
        ?>

    <h3>Geral do período</h3>
    <table>
        <thead>
            <tr>
                <th>Total de Entrada</th>
                <th>Total de Saída</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>R$ {{ number_format($totalEntrada, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($totalSaida, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($saldo, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Movimentações</h3>
    @if(count($movements) > 0)
        <table>
            <thead>
                <tr>
                    <th>Banco</th>
                    <th>Conta</th>
                    <th>Agência</th>
                    <th>Categoria</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Data de movimentação</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                    <tr>
                        <td>{{ $movement['account']['name'] }}</td>
                        <td>{{ $movement['account']['account_number'] }}</td>
                        <td>{{ $movement['account']['agency_number'] }}</td>
                        <td>{{ $movement['category']['name'] }}</td>
                        <td>R$ {{ number_format($movement['value'], 2, ',', '.') }}</td>
                        <td style="color: {{ $movement['type'] === 'entrada' ? 'green' : 'red' }}">
                            {{ ucfirst($movement['type']) }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($movement['date_movement'])->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-records">Não há movimentações registradas.</p>
    @endif

</body>

</html>