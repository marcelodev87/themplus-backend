<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Enterprise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SetCategoriesByEnterpriseSeeder extends Seeder
{
    public function run()
    {
        $enterpriseIds = Enterprise::pluck('id');

        $oldCategories = Category::whereNull('enterprise_id')->pluck('id')->toArray();

        $newCategories = [];
        $categoryMapping = [];

        foreach ($enterpriseIds as $id) {
            $categories = [
                ['name' => 'Dízimos', 'type' => 'entrada'],
                ['name' => 'Ofertas', 'type' => 'entrada'],
                ['name' => 'Doações', 'type' => 'entrada'],
                ['name' => 'Campanha', 'type' => 'entrada'],
                ['name' => 'Energia elétrica', 'type' => 'saída'],
                ['name' => 'Água/Esgoto', 'type' => 'saída'],
                ['name' => 'Material expediente', 'type' => 'saída'],
                ['name' => 'Supermercado', 'type' => 'saída'],
                ['name' => 'Recebimentos', 'type' => 'entrada'],
                ['name' => 'Honorários profissionais', 'type' => 'saída'],
                ['name' => 'Outros', 'type' => 'entrada'],
                ['name' => 'Outros', 'type' => 'saída'],
                ['name' => 'Telefone/Tv/Internet', 'type' => 'saída'],
                ['name' => 'Taxas/Impostos', 'type' => 'saída'],
                ['name' => 'Aluguel', 'type' => 'saída'],
                ['name' => 'Prebenda pastoral', 'type' => 'saída'],
                ['name' => 'Material de construção', 'type' => 'saída'],
                ['name' => 'Cartão de crédito', 'type' => 'saída'],
                ['name' => 'Móveis e utensílios', 'type' => 'saída'],
                ['name' => 'Computadores e periféricos', 'type' => 'saída'],
                ['name' => 'Instrumentos e equipamentos', 'type' => 'saída'],
                ['name' => 'Compra de imóvel', 'type' => 'saída'],
                ['name' => 'Compra de imóvel - parcela', 'type' => 'saída'],
                ['name' => 'Convenção', 'type' => 'saída'],
                ['name' => 'Missões', 'type' => 'saída'],
                ['name' => 'Material EBD', 'type' => 'saída'],
                ['name' => 'Plano de saúde', 'type' => 'saída'],
                ['name' => 'Saldo inicial', 'type' => 'entrada'],
                ['name' => 'Transferência', 'type' => 'entrada'],
                ['name' => 'Transferência', 'type' => 'saída'],
                ['name' => 'Combustível/Transporte', 'type' => 'saída'],
                ['name' => 'Ajuda de custo', 'type' => 'saída'],
                ['name' => 'Tarifa bancária', 'type' => 'saída'],
                ['name' => 'Deposito', 'type' => 'entrada'],
                ['name' => 'Saque', 'type' => 'saída'],
            ];

            foreach ($categories as $category) {
                $newId = Str::uuid();
                $newCategories[] = [
                    'id' => $newId,
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'enterprise_id' => $id,
                    'default' => 1,
                ];
                $categoryMapping[$category['name']] = $newId;
            }
        }

        Category::insert($newCategories);

        foreach ($categoryMapping as $name => $newId) {
            DB::table('movements')->whereIn('category_id', $oldCategories)->update(['category_id' => $newId]);
            DB::table('schedulings')->whereIn('category_id', $oldCategories)->update(['category_id' => $newId]);
        }

        Category::whereNull('enterprise_id')->delete();
    }
}
