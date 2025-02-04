<?php

namespace App\Helpers;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryHelper
{
    public static function existsCategory($name, $type, $enterpriseId)
    {
        $categoryRepository = new CategoryRepository(new Category);

        $category = $categoryRepository->findByNameAndType($name, $type, $enterpriseId);
        if ($category) {
            throw new \Exception("Já existe uma categoria igual ou parecida para o tipo: {$type}");
        }
    }

    public static function createDefault($enterpriseId)
    {
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
            Category::create([
                'name' => $category['name'],
                'type' => $category['type'],
                'enterprise_id' => $enterpriseId,
                'default' => 1,
            ]);
        }
    }
}
