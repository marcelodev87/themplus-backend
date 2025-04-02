<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetAlertInCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = DB::table('categories')->get();

        foreach ($categories as $category) {
            $name = strtolower($category->name);
            $type = strtolower($category->type);

            if ($name === 'convenção' && $type === 'saída') {
                $alert = 'Confirmo que anexei o recibo desta operação';
            } elseif ($name === 'honorários profissionais' && $type === 'saída') {
                $alert = 'Confirmo que anexei a Nota Fiscal desta operação';
            } elseif ($name === 'saldo inicial' && $type === 'entrada') {
                $alert = null;
            } elseif ($name === 'deposito' && $type === 'entrada') {
                $alert = 'Confirmo que anexei o recibo desta operação';
            } elseif ($name === 'material de construção' && $type === 'saída') {
                $alert = 'Confirmo que anexei a Nota Fiscal desta operação';
            } elseif ($name === 'telefone/tv/internet' && $type === 'saída') {
                $alert = 'Confirmo que anexei a fatura desta operação';
            } elseif ($name === 'aluguel' && $type === 'saída') {
                $alert = 'Estou ciente de que devo fazer contato com minha assessoria contábil para verificar a necessidade de pagamento de impostos';
            } elseif ($name === 'plano de saúde' && $type === 'saída') {
                $alert = 'Este contrato deve estar vinculado ao CNPJ da instituição. Confirmo que anexei a fatura desta operação';
            } elseif ($name === 'taxas/impostos' && $type === 'saída') {
                $alert = 'Confirmo que fiz o anexo da Guia de Cobrança/Comprovante de pagamento';
            } elseif ($name === 'energia elétrica' && $type === 'saída') {
                $alert = 'Confirmo que anexei a fatura desta operação';
            } elseif ($name === 'compra de imóvel - parcela' && $type === 'saída') {
                $alert = 'Confirmo que anexei do contrato da operação';
            } elseif ($name === 'computadores e periféricos' && $type === 'saída') {
                $alert = 'Confirmo que anexei a Nota Fiscal desta operação';
            } elseif ($name === 'tarifa bancária' && $type === 'saída') {
                $alert = null;
            } elseif ($name === 'supermercado' && $type === 'saída') {
                $alert = 'Confirmo que anexei a Nota Fiscal desta operação';
            } elseif ($name === 'recebimentos' && $type === 'entrada') {
                $alert = null;
            } elseif ($name === 'compra de imóvel' && $type === 'saída') {
                $alert = 'Confirmo que anexei o contrato desta operação';
            } else {
                continue;
            }

            DB::table('categories')
                ->where('id', $category->id)
                ->update(['alert' => $alert]);
        }
    }
}
