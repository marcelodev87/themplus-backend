<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Repositories\AccountRepository;
use App\Repositories\SettingsCounterRepository;
use App\Helpers\CategoryHelper;

class TransferEnterpriseSeeder extends Seeder
{
    protected $accountRepository;
    protected $settingsCounterRepository;

    public function __construct(AccountRepository $accountRepository, SettingsCounterRepository $settingsCounterRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
    }

    public function run()
    {
        $filePath = public_path('storage/imports/lista_igreja.xlsx');

        $subscription = DB::table('subscriptions')
            ->where('name', 'free')
            ->first();

        (new FastExcel)->import($filePath, function ($row) use ($subscription) {
            $enterprise = Enterprise::create([
                'name' => $row['nome_empresa'],
                'cnpj' => (strlen($row['cnpj_cpf_empresa']) >= 12) ? strval($row['cnpj_cpf_empresa']) : null,
                'cpf' => (strlen($row['cnpj_cpf_empresa']) < 12) ? strval($row['cnpj_cpf_empresa']) : null,
                'cep' => (strlen($row['cep']) > 3) ? strval($row['cep']) : null,
                'city' => $row['cidade'],
                'state' => $row['state'],
                'neighborhood' => $row['bairro'],
                'address' => $row['endereco'],
                'number_address' => $row['número'],
                'complement' => $row['complemento'],
                'subscription_id' => $subscription->id,
                'code_financial' => $row['sistema_contabil']
            ]);

            $dataAccount = ['name' => 'Caixinha', 'enterprise_id' => $enterprise->id];
            $this->accountRepository->create($dataAccount);
            $this->settingsCounterRepository->create(['enterprise_id' => $enterprise->id]);
            CategoryHelper::createDefault($enterprise->id);
        });
    }
}
