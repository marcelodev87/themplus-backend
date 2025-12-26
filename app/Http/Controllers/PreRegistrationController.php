<?php

namespace App\Http\Controllers;

use App\Helpers\PreRegistrationConfigHelper;
use App\Repositories\PreRegistrationRelationshipRepository;
use App\Repositories\PreRegistrationRepository;
use App\Rules\PreRegistrationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreRegistrationController
{
    private $repository;

    private $relationshipRepository;

    private $rule;

    public function __construct(PreRegistrationRepository $repository, PreRegistrationRule $rule, PreRegistrationRelationshipRepository $relationshipRepository)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->relationshipRepository = $relationshipRepository;
    }

    public function index(Request $request)
    {
        try {
            $registrations = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['relationships']);

            return response()->json(['pre_registration' => $registrations], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar pré-registros de membros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->create($request);

            PreRegistrationConfigHelper::isFormActive($request->input('enterpriseID'));

            $member = $this->repository->create([
                'enterprise_id' => $request->input('enterpriseID'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'phone' => $request->input('phone'),
                'description' => $request->input('description'),
                'profession' => $request->input('profession'),
                'naturalness' => $request->input('naturalness'),
                'education' => $request->input('education'),
                'cpf' => $request->input('cpf'),
                'date_birth' => $request->input('dateBirth'),
                'marital_status' => $request->input('maritalStatus'),
                'date_baptismo' => $request->input('dateBaptismo'),
                'cep' => $request->input('cep'),
                'uf' => $request->input('uf'),
                'address' => $request->input('address'),
                'address_number' => $request->input('addressNumber'),
                'neighborhood' => $request->input('neighborhood'),
                'city' => $request->input('city'),
                'complement' => $request->input('complement'),
            ]);

            if ($member && $request->filled('relationship')) {
                foreach ($request->input('relationship') as $relationship) {
                    $this->relationshipRepository->create([
                        'pre_registration_id' => $member->id,
                        'member' => $relationship['member'],
                        'kinship' => $relationship['kinship'],
                    ]);
                }
            }

            if ($member) {
                DB::commit();

                return response()->json(['message' => 'Solicitação enviada com sucesso'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao solicitar : '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $registration = $this->repository->delete($id);

            if ($registration) {
                DB::commit();

                $registrations = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['relationships']);

                return response()->json(['pre_registration' => $registrations, 'message' => 'Pré-registro excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar pré-registro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar pré-registro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
