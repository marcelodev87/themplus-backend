<?php

namespace App\Repositories;

use App\Models\Enterprise;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnterpriseRepository
{
    protected $model;

    public function __construct(Enterprise $enterprise)
    {
        $this->model = $enterprise;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllOfficesByEnterprise($enterpriseId)
    {
        return $this->model->with('users')
            ->where('created_by', $enterpriseId)
            ->get();
    }

    public function getAllViewEnterprises($enterpriseId)
    {
        return $this->model->select('id', 'name')
            ->where('created_by', $enterpriseId)
            ->get();
    }

    public function getBonds($enterpriseId)
    {
        return $this->model->select('id', 'name', 'cnpj', 'cpf', 'email', 'phone', 'code_financial', 'created_by')
            ->where('counter_enterprise_id', $enterpriseId)->get();
    }

    public function searchEnterprise($enterpriseId, $text)
    {
        return $this->model->where('id', '!=', $enterpriseId)
            ->whereNull('counter_enterprise_id')
            ->whereNull('created_by')
            ->where('position', 'client')
            ->where(function ($query) use ($text) {
                $query->where('cpf', 'like', "%{$text}%")
                    ->orWhere('cnpj', 'like', "%{$text}%")
                    ->orWhere('email', 'like', "%{$text}%");
            })
            ->select('id', 'name', 'email', 'cpf', 'cnpj')
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByCpf($cpf)
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function findByCnpj($cnpj)
    {
        return $this->model->where('cnpj', $cnpj)->first();
    }

    public function createStart($data)
    {
        return $this->model->create($data);
    }

    public function createOffice(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            $enterprise->update($data);

            return $enterprise;
        }

        return null;
    }

    public function deleteBond($id)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            $enterprise->update(['counter_enterprise_id' => null]);

            $offices = $this->getAllOfficesByEnterprise($id);
            foreach ($offices as $office) {
                $office->update(['counter_enterprise_id' => null]);
            }

            return $enterprise;
        }

        return null;
    }

    public function deleteOffice($id)
{
    $enterprise = $this->findById($id);
    
    if ($enterprise) {
        $movements = DB::table('movements')->where('enterprise_id', $id)->get();
        foreach ($movements as $movement) {
            if ($movement->receipt) {
                $oldFilePath = str_replace(env('AWS_URL') . '/', '', $movement->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }
        }
        DB::table('movements')->where('enterprise_id', $id)->delete();

        $schedulings = DB::table('schedulings')->where('enterprise_id', $id)->get();
        foreach ($schedulings as $scheduling) {
            if ($scheduling->receipt) {
                $oldFilePath = str_replace(env('AWS_URL') . '/', '', $scheduling->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }
        }
        DB::table('schedulings')->where('enterprise_id', $id)->delete();

        $financial_receipts = DB::table('financial_movements_receipts')->where('enterprise_id', $id)->get();
        foreach ($financial_receipts as $fr) {
            if ($fr->receipt) {
                $oldFilePath = str_replace(env('AWS_URL') . '/', '', $fr->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }
        }
        DB::table('financial_movements_receipts')->where('enterprise_id', $id)->delete();

        DB::table('accounts')->where('enterprise_id', $id)->delete();
        DB::table('categories')->where('enterprise_id', $id)->delete();

        DB::table('users')->where('enterprise_id', $id)
            ->whereNotNull('department_id')
            ->update(['department_id' => null]);
            
        DB::table('departments')->where('enterprise_id', $id)->delete();
        DB::table('feedbacks')->where('enterprise_id', $id)->delete();
        DB::table('financial_movements')->where('enterprise_id', $id)->delete();
        DB::table('orders')->where('enterprise_id', $id)->delete();
        DB::table('registers')->where('enterprise_id', $id)->delete();
        DB::table('notifications')->where('enterprise_id', $id)->delete();
        DB::table('users')->where('enterprise_id', $id)->delete();
        DB::table('settings_counter')->where('enterprise_id', $id)->delete();

        // Remover a empresa
        return $enterprise->delete();
    }

    return false;
}


    public function delete($id)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {

            $offices = DB::table('enterprises')->where('created_by', $enterprise->id)->get();

            // Deletando dados de filiais
            foreach ($offices as $office) {
                $movements = DB::table('movements')->where('enterprise_id', $office->id)->get();
                foreach ($movements as $movement) {
                    if ($movement->receipt) {
                        $oldFilePath = str_replace(env('AWS_URL') . '/', '', $movement->receipt);
                        Storage::disk('s3')->delete($oldFilePath);
                    }
                }
                DB::table('movements')->where('enterprise_id', $office->id)->delete();

                $schedulings = DB::table('schedulings')->where('enterprise_id', $office->id)->get();
                foreach ($schedulings as $scheduling) {
                    if ($scheduling->receipt) {
                        $oldFilePath = str_replace(env('AWS_URL') . '/', '', $scheduling->receipt);
                        Storage::disk('s3')->delete($oldFilePath);
                    }
                }
                DB::table('schedulings')->where('enterprise_id', $office->id)->delete();

                $financial_receipts = DB::table('financial_movements_receipts')->where('enterprise_id', $office->id)->get();
                foreach ($financial_receipts as $fr) {
                    if ($fr->receipt) {
                        $oldFilePath = str_replace(env('AWS_URL') . '/', '', $fr->receipt);
                        Storage::disk('s3')->delete($oldFilePath);
                    }
                }

                DB::table('accounts')->where('enterprise_id', $office->id)->delete();
                DB::table('categories')->where('enterprise_id', $office->id)->delete();

                DB::table('users')->where('enterprise_id', $office->id)
                    ->whereNotNull('department_id')
                    ->update(['department_id' => null]);
                DB::table('departments')->where('enterprise_id', $office->id)->delete();

                DB::table('feedbacks')->where('enterprise_id', $office->id)->delete();
                DB::table('financial_movements')->where('enterprise_id', $office->id)->delete();
                DB::table('orders')->where('enterprise_id', $office->id)->delete();
                DB::table('registers')->where('enterprise_id', $office->id)->delete();
                DB::table('notifications')->where('enterprise_id', $office->id)->delete();
                DB::table('users')->where('enterprise_id', $office->id)->delete();
                DB::table('settings_counter')->where('enterprise_id', $office->id)->delete();

                DB::table('enterprises')->where('id', $office->id)->delete();
            }

            // Deletando dados da matriz
            $movements = DB::table('movements')->where('enterprise_id', $id)->get();
            foreach ($movements as $movement) {
                if ($movement->receipt) {
                    $oldFilePath = str_replace(env('AWS_URL') . '/', '', $movement->receipt);
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }
            DB::table('movements')->where('enterprise_id', $id)->delete();

            $schedulings = DB::table('schedulings')->where('enterprise_id', $id)->get();
            foreach ($schedulings as $scheduling) {
                if ($scheduling->receipt) {
                    $oldFilePath = str_replace(env('AWS_URL') . '/', '', $scheduling->receipt);
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }
            DB::table('schedulings')->where('enterprise_id', $id)->delete();

            $financial_receipts = DB::table('financial_movements_receipts')->where('enterprise_id', $id)->get();
            foreach ($financial_receipts as $fr) {
                if ($fr->receipt) {
                    $oldFilePath = str_replace(env('AWS_URL') . '/', '', $fr->receipt);
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }
            DB::table('accounts')->where('enterprise_id', $id)->delete();
            DB::table('categories')->where('enterprise_id', $id)->delete();

            DB::table('users')->where('enterprise_id', $id)
                ->whereNotNull('department_id')
                ->update(['department_id' => null]);
            DB::table('departments')->where('enterprise_id', $id)->delete();

            DB::table('feedbacks')->where('enterprise_id', $id)->delete();
            DB::table('financial_movements')->where('enterprise_id', $id)->delete();
            DB::table('orders')->where('enterprise_id', $id)->delete();
            DB::table('registers')->where('enterprise_id', $id)->delete();
            DB::table('notifications')->where('enterprise_id', $id)->delete();
            DB::table('users')->where('enterprise_id', $id)->delete();
            DB::table('settings_counter')->where('enterprise_id', $id)->delete();

            return $enterprise->delete();
        }

        return false;
    }
}
