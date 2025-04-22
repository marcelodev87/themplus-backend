<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class PopulateClient1Seeder extends Seeder
{
    public function run()
    {
        $enterprise = DB::table('enterprises')->where('id', '726bcd7c-9fd7-4ca8-a289-61f6729113d5')->first();

        $account = Account::create([
            'name' => 'Bradesco2',
            'balance' => 0.00,
            'enterprise_id' => $enterprise->id,
            'active' => 1,
        ]);

        $categories = [
            'dizimos' => DB::table('categories')->where('name', 'Dízimos')->where('enterprise_id', $enterprise->id)->first(),
            'seguros' => DB::table('categories')->where('name', 'Seguros')->where('enterprise_id', $enterprise->id)->first(),
            'cartao_credito' => DB::table('categories')->where('name', 'Cartão de crédito')->where('enterprise_id', $enterprise->id)->first(),
            'deposito' => DB::table('categories')->where('name', 'Deposito')->where('enterprise_id', $enterprise->id)->first(),
            'emprestimos' => DB::table('categories')->where('name', 'Empréstimos')->where('enterprise_id', $enterprise->id)->first(),
            'folha_pagamento' => DB::table('categories')->where('name', 'Folha de pagamento de funcionários')->where('enterprise_id', $enterprise->id)->first(),
            'honorarios' => DB::table('categories')->where('name', 'Honorários profissionais')->where('enterprise_id', $enterprise->id)->first(),
            'ofertas' => DB::table('categories')->where('name', 'Ofertas')->where('enterprise_id', $enterprise->id)->first(),
            'tarifa_bancaria' => DB::table('categories')->where('name', 'Tarifa bancária')->where('enterprise_id', $enterprise->id)->first(),
            'telefone' => DB::table('categories')->where('name', 'Telefone/Tv/Internet')->where('enterprise_id', $enterprise->id)->first(),
            'impostos' => DB::table('categories')->where('name', 'Taxas/Impostos')->where('enterprise_id', $enterprise->id)->first(),
            'missoes' => DB::table('categories')->where('name', 'Missões')->where('enterprise_id', $enterprise->id)->first(),
            'transferencia' => DB::table('categories')->where('name', 'Transferência')->where('enterprise_id', $enterprise->id)->where('type', 'entrada')->first(),
        ];

        $filePath = public_path('storage/imports/populate_client1.xlsx');

        (new FastExcel)->import($filePath, function ($row) use ($enterprise, $account, $categories) {
            if (empty($row['dia']) || empty($row['historico']) || empty($row['valor'])) {
                return;
            }

            $categoria = $row['Categoria'] ?? '';
            $category = match ($categoria) {
                'Ação Social' => $categories['impostos'],
                'Cartão de Crédito' => $categories['cartao_credito'],
                'Depósito' => $categories['deposito'],
                'Dízimo', 'Dízimos' => $categories['dizimos'],
                'Empréstimos' => $categories['emprestimos'],
                'Folha de Pagamento' => $categories['folha_pagamento'],
                'Honorários' => $categories['honorarios'],
                'Impostos' => $categories['impostos'],
                'Oferta' => $categories['ofertas'],
                'Seguros' => $categories['seguros'],
                'Tarifa Bancária' => $categories['tarifa_bancaria'],
                'Transferência' => $categories['transferencia'],
                default => $categories['telefone'],
            };

            $dateFormatted = null;
            if (!empty($row['dia'])) {
                try {
                    $dateFormatted = Carbon::createFromFormat('d/m/Y', trim($row['dia']))->format('Y-m-d');
                } catch (\Exception $e) {
                }
            }

            Movement::create([
                'description' => $row['historico'],
                'value' => $row['valor'],
                'date_movement' => $dateFormatted,
                'account_id' => $account->id,
                'enterprise_id' => $enterprise->id,
                'type' => $category->type,
                'category_id' => $category->id
            ]);
        });

    }

}
