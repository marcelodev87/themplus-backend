<!DOCTYPE html>
<html>

<head>
    <title>Dashboard PDF {{ str_replace('-', '/', $date) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
            background-color: #fff;
            padding: 10px;
        }

        h1 {
            text-align: center;
            color: #660000;
        }

        h3 {
            color: #2e2e2e;
            border-bottom: 2px solid #660000;
            padding-bottom: 5px;
        }

        p {
            font-size: 1.1em;
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
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #660000;
            color: white;
            font-size: 1.2em;
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
        }

        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.5em;
            color: #660000;
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
    <h1>Relatório do Dashboard {{ str_replace('-', '/', $date) }}</h1>

    <h3>Dados de Movimentação</h3>
    <div class="card">
        <div class="card-title">Resumo de Movimentações</div>
        <div class="card-content">
            <div>
                <strong>Entradas:</strong> R$ {{ number_format($movements_dashboard['entry_value'], 2, ',', '.') }}<br>
                <strong>Saídas:</strong> R$ {{ number_format($movements_dashboard['out_value'], 2, ',', '.') }}<br>
                <strong>Saldo:</strong> R$ {{ number_format($movements_dashboard['balance'], 2, ',', '.') }}<br>
            </div>
            <div>
                <strong>Mês/Ano:</strong> {{ $movements_dashboard['month_year'] }}<br>
            </div>
        </div>
    </div>

    <h3>Dados de Agendamentos</h3>
    <div class="card">
        <div class="card-title">Resumo de Agendamentos</div>
        <div class="card-content">
            <div>
                <strong>Entradas:</strong> R$
                {{ number_format($schedulings_dashboard['entry_value'], 2, ',', '.') }}<br>
                <strong>Saídas:</strong> R$ {{ number_format($schedulings_dashboard['out_value'], 2, ',', '.') }}<br>
            </div>
            <div>
                <strong>Mês/Ano:</strong> {{ $schedulings_dashboard['month_year'] }}<br>
            </div>
        </div>
    </div>

    <h3>Usuários</h3>
    <table>
        <thead>
            <tr>
                <th>Total de Usuários</th>
                <th>Administradores</th>
                <th>Usuários Comuns</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $users_dashboard['amount_users'] }}</td>
                <td>{{ $users_dashboard['amount_admins'] }}</td>
                <td>{{ $users_dashboard['amount_common_users'] }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Geral da organização</h3>
    <table>
        <thead>
            <tr>
                <th>Entrada </th>
                <th>Saída</th>
                <th>Salvo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>R$ {{ number_format($general['entry'], 2, ',', '.') }}</td>
                <td>R$ {{ number_format($general['out'], 2, ',', '.') }}</td>
                <td>R$ {{ number_format($general['entry'] - $general['out'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Contas</h3>
    <p>A sua organização possui as seguintes contas:</p>
    <table>
        <thead>
            <tr>
                <th>Nome da Conta</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts_dashboard['accounts'] as $account)
                <tr>
                    <td>{{ $account['name'] }}</td>
                    <td>R$ {{ number_format($account['balance'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Movimentações</h3>
    <p>A sua organização possui as seguintes movimentações x categorias:</p>
    @if(count($categories_movements_dashboard) > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome da Categoria</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories_movements_dashboard as $movement)
                    <tr>
                        <td>{{ $movement['name'] }}</td>
                        <td>
                            <span class="{{ $movement['type'] === 'entrada' ? 'entrada' : 'saida' }}">
                                {{ ucfirst($movement['type']) }}
                            </span>
                        </td>
                        <td>R$ {{ number_format($movement['value'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-records">Não há movimentações registradas.</p>
    @endif

    <h3>Agendamentos</h3>
    <p>A sua organização possui os seguintes agendamentos x categorias:</p>
    @if(count($categories_schedules_dashboard) > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome da Categoria</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories_schedules_dashboard as $schedule)
                    <tr>
                        <td>{{ $schedule['name'] }}</td>
                        <td>
                            <span class="{{ $schedule['type'] === 'entrada' ? 'entrada' : 'saida' }}">
                                {{ ucfirst($schedule['type']) }}
                            </span>
                        </td>
                        <td>R$ {{ number_format($schedule['value'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-records">Não há agendamentos registrados.</p>
    @endif

</body>

</html>