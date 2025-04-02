<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateNewCategoriesSeeder extends Seeder
{
    public function run()
    {
        $enterprises = DB::table('enterprises')->select('id')->get();

        foreach ($enterprises as $enterprise) {
            Category::create([
                'name' => 'Seguros',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei o contrato desta operação',
            ]);

            Category::create([
                'name' => 'Consórcios',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei o contrato desta operação',
            ]);

            Category::create([
                'name' => 'Empréstimos',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei o contrato desta operação',
            ]);

            Category::create([
                'name' => 'Financiamentos',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei o contrato desta operação',
            ]);

            Category::create([
                'name' => 'Alimentação',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei a Nota Fiscal desta operação',
            ]);

            Category::create([
                'name' => 'Aplicação financeira',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Confirmo que anexei o contrato desta operação',
            ]);

            Category::create([
                'name' => 'Folha de pagamento de funcionários',
                'enterprise_id' => $enterprise->id,
                'type' => 'saída',
                'active' => 1,
                'default' => 1,
                'alert' => 'Estou ciente de que devo fazer contato com minha assessoria contábil para efetuar esta operação de acordo com as regras da CLT',
            ]);
        }
    }
}
