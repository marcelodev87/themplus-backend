<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Dízimos','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Ofertas','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Doações','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Campanha','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Energia elétrica','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Água/Esgoto','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Material expediente','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Supermercado','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Recebimentos','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Honorários profissionais','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Outros','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Outros','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Telefone/Tv/Internet','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Taxas/Impostos','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Aluguel','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Prebenda pastoral','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Material de construção','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Cartão de crédito','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Móveis e utensílios','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Computadores e periféricos','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Instrumentos e equipamentos','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Compra de imóvel','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Compra de imóvel - parcela','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Convenção','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Missões','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Material EBD','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Plano de saúde','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Saldo inicial','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Transferências entre contas','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Combustível/Transporte','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Ajuda de custo','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Tarifa bancária','type'=> 'saída' ,'enterprise_id' => null]);
        Category::create(['name' => 'Deposito','type'=> 'entrada' ,'enterprise_id' => null]);
        Category::create(['name' => 'Saque','type'=> 'saída' ,'enterprise_id' => null]);
    }

    public function rollback()
    {
        Category::where('enterprise_id',null)->delete();
    }
}
