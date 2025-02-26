<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\UserResource;
use App\Repositories\EnterpriseRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SettingsCounterRepository;
use App\Repositories\UserRepository;
use App\Rules\UserRule;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberController
{
    private $service;

    private $repository;

    private $rule;

    private $enterpriseRepository;

    private $settingsCounterRepository;

    private $notificationRepository;

    public function __construct(UserService $service, UserRepository $repository, UserRule $rule, EnterpriseRepository $enterpriseRepository, SettingsCounterRepository $settingsCounterRepository, NotificationRepository $notificationRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
        $this->notificationRepository = $notificationRepository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json([
                'users' => UserResource::collection($users),
                'filled_data' => $filledData,
                'notifications' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os membros da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function inbox(Request $request)
    {
        try {
            $inbox = $this->notificationRepository->getInbox($request->user()->id);

            return response()->json([
                'inbox' => $inbox,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as notificações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function readNotification(Request $request)
    {
        try {
            $read = $this->notificationRepository->update($request->input('id'), ['read' => 1]);
            if ($read) {
                $inbox = $this->notificationRepository->getInbox($request->user()->id);

                return response()->json([
                    'inbox' => $inbox,
                    'message' => 'Notificação marcada como lida',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao marcar notificação como lida: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function readAllNotification(Request $request)
    {
        try {
            $read = $this->notificationRepository->updateAll($request->user()->id);
            if ($read) {
                $inbox = $this->notificationRepository->getInbox($request->user()->id);

                return response()->json([
                    'inbox' => $inbox,
                    'message' => 'Todas as notificações marcadas como lida',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao marcar todas as notificações como lida: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexByEnterprise($id)
    {
        try {
            $users = $this->repository->getAllByEnterpriseWithRelations($id);
            $settings = $this->settingsCounterRepository->getByEnterprise($id);

            return response()->json(['users' => UserResource::collection($users), 'settings' => $settings], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os membros da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->repository->findById($id);

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados do usuário: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->include($request);
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'member',
                "{$user->name}|{$user->email}"
            );

            if ($user && $register) {
                DB::commit();
                $dataNotification = [
                    'user_id' => $user->id,
                    'enterprise_id' => $user->enterprise_id,
                    'title' => 'Boas vindas ao Themplus',
                    'text' => 'Seja bem-vindo ao Themplus! Você acaba de dar o primeiro passo para gerenciar melhor suas movimentações e simplificar a burocracia da sua contabilidade de modo mais fácil. Estamos aqui para ajudar você a ter uma experiência mais organizada e eficiente. Aproveite todos os recursos que preparamos para otimizar a sua gestão!',
                ];
                $this->notificationRepository->createForUser($dataNotification);

                $enterpriseId = $request->user()->enterprise_id !== $request->user()->view_enterprise_id ? $request->user()->view_enterprise_id : $request->user()->enterprise_id;
                $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['users' => UserResource::collection($users), 'message' => 'Membro adicionado á sua organização com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro da organização');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storeByCounter(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->storeByCounter($request);
            $enterprise = $this->enterpriseRepository->findById($user->enterprise_id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'manageUser',
                "{$user->name}|{$user->email}|{$enterprise->name}|{$enterprise->email}"
            );

            if ($user && $register) {
                DB::commit();

                $users = $this->repository->getAllByEnterpriseWithRelations($request->input('enterpriseId'));
                $settings = $this->settingsCounterRepository->getByEnterprise($request->input('enterpriseId'));

                return response()->json(['users' => UserResource::collection($users), 'settings' => $settings, 'message' => 'Membro adicionado á sua organização com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro da organização');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function startOfficeNewUser(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->startOfficeNewUser($request);

            if ($user) {
                DB::commit();

                $dataNotification = [
                    'user_id' => $user->id,
                    'enterprise_id' => $user->enterprise_id,
                    'title' => 'Boas vindas ao Themplus',
                    'text' => 'Seja bem-vindo ao Themplus! Você acaba de dar o primeiro passo para gerenciar melhor suas movimentações e simplificar a burocracia da sua contabilidade de modo mais fácil. Estamos aqui para ajudar você a ter uma experiência mais organizada e eficiente. Aproveite todos os recursos que preparamos para otimizar a sua gestão!',
                ];
                $this->notificationRepository->createForUser($dataNotification);

                $enterpriseId = $request->user()->enterprise_id;
                $offices = $this->enterpriseRepository->getAllOfficesByEnterprise($enterpriseId);

                return response()->json(['offices' => OfficeResource::collection($offices), 'message' => 'Membro adicionado á filial com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro para filial');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro da filial: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $enterpriseId = $request->user()->view_enterprise_id;

        $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "users_{$enterpriseId}_{$dateTime}.xlsx";

        return (new UserExport($users))->download($fileName);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $member = $this->repository->findById($request->input('id'));
            $user = $this->service->updateMember($request);
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'member',
                "{$member->name}|{$member->email}"
            );

            if ($user && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id !== $request->user()->view_enterprise_id ? $request->user()->view_enterprise_id : $request->user()->enterprise_id;
                $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['users' => UserResource::collection($users), 'message' => 'Dados do membro foram atualizados com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar dados do membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function active(Request $request)
    {
        try {
            DB::beginTransaction();
            $member = $this->repository->update($request->input('userId'), ['active' => $request->input('active')]);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                $request->input('active') === 1 ? 'reactivated' : 'inactivated',
                'member',
                "{$member->name}|{$member->email}"
            );
            if ($member && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id !== $request->user()->view_enterprise_id ? $request->user()->view_enterprise_id : $request->user()->enterprise_id;
                $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                $message = $request->input('active') == 0 ? 'Usuário inativado com sucesso' : 'Usuário ativado com sucesso';

                return response()->json(['users' => UserResource::collection($users), 'message' => $message], 200);
            }

            throw new \Exception('Falha ao atualizar dados do membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateByCounter(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->updateByCounter($request);
            $enterprise = $this->enterpriseRepository->findById($user->enterprise_id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'manageUser',
                "{$user->name}|{$user->email}|{$enterprise->name}|{$enterprise->email}"
            );

            if ($user && $register) {
                DB::commit();

                $users = $this->repository->getAllByEnterpriseWithRelations($user->enterprise_id);
                $settings = $this->settingsCounterRepository->getByEnterprise($user->enterprise_id);

                return response()->json(['users' => UserResource::collection($users), 'settings' => $settings, 'message' => 'Membro atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar dados do membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $memberDelete = $this->repository->findById($id);
            $member = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'member',
                "{$memberDelete->name}|{$memberDelete->email}"
            );

            if ($member && $register) {
                DB::commit();

                return response()->json(['message' => 'Membro deletado com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroyNotification($id)
    {
        try {
            DB::beginTransaction();
            $notification = $this->notificationRepository->delete($id);
            if ($notification) {
                DB::commit();

                return response()->json(['message' => 'Notificação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar notificação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar notificação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroyByCounter(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $memberDelete = $this->repository->findById($id);
            $enterprise = $this->enterpriseRepository->findById($memberDelete->enterprise_id);

            $member = $this->service->destroyByCounter($request, $id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'manageUser',
                "{$memberDelete->name}|{$memberDelete->email}|{$enterprise->name}|{$enterprise->email}"
            );

            if ($member && $register) {
                DB::commit();

                return response()->json(['message' => 'Membro deletado com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar membro pela contabilidade');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar membro pela contabilidade: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
