<?php

namespace App\Http\Controllers;

use App\Enums\Subscription;
use App\Helpers\NotificationsHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\CreditCardService;
use App\Services\PixService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController
{
    protected $repository;

    protected $creditCardService;

    protected $pixService;

    protected $service;

    protected $enterpriseRepository;

    protected $notificationRepository;

    public function __construct(SubscriptionRepository $repository, CreditCardService $creditCardService, PixService $pixService, SubscriptionService $service, EnterpriseRepository $enterpriseRepository, NotificationRepository $notificationRepository)
    {
        $this->enterpriseRepository = $enterpriseRepository;
        $this->repository = $repository;
        $this->creditCardService = $creditCardService;
        $this->pixService = $pixService;
        $this->service = $service;
        $this->notificationRepository = $notificationRepository;
    }

    public function index(Request $request)
    {
        try {
            $notifications = NotificationsHelper::getNoRead($request->user()->id);
            $subscriptions = $this->repository->getAll();

            return response()->json(['subscriptions' => $subscriptions, 'notifications' => $notifications], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar as assinaturas: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function paymentCreditCard(Request $request)
    {
        try {
            DB::beginTransaction();
            $result = $this->creditCardService->payment($request);

            $this->enterpriseRepository->update(
                $request->user()->enterprise_id,
                [
                    'allow_test_subscription' => 0,
                ]
            );

            if ($result) {
                DB::commit();
                $notifications = NotificationsHelper::getNoRead($request->user()->id);

                return response()->json(['result' => $result, 'notifications' => $notifications], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao fazer pagamento com cartão de crédito: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function test(Request $request)
    {
        try {
            DB::beginTransaction();
            $enterprise = $this->enterpriseRepository->findById(
                $request->user()->enterprise_id
            );

            if ($enterprise->allow_test_subscription !== 1) {
                throw new \Exception('Teste gratuito não permitido para esta empresa');
            }

            $timezone = 'America/Sao_Paulo';

            $now = now($timezone);

            $expiredDate = $now
                ->copy()
                ->addDays(7);

            $subscription = $this->repository->findById($request->subscriptionID);
            if (! $subscription) {
                throw new \Exception('Assinatura não válida');
            }

            $result = $this->enterpriseRepository->update(
                $request->user()->enterprise_id,
                [
                    'subscription_id' => $subscription->id,
                    'allow_test_subscription' => 0,
                    'expired_date' => $expiredDate->toDateTimeString(),
                ]
            );

            $user = $request->user();
            $subscriptionName = Subscription::from($subscription->name)->label();
            $expiredDateFormatted = $expiredDate->format('d/m/Y H:i:s');

            $this->notificationRepository->create(
                $user->enterprise_id,
                'Teste gratuito ativado!',
                sprintf(
                    "O usuário %s ativou o período de teste gratuito.\n".
                    "Detalhes do Teste:\n".
                    "Plano: %s\n".
                    'Válido até: %s',
                    $user->name,
                    $subscriptionName,
                    $expiredDateFormatted
                )
            );

            if ($result) {

                DB::commit();
                $notifications = NotificationsHelper::getNoRead(
                    $request->user()->id
                );

                return response()->json([
                    'message' => 'Teste gratuito por 7 dias ativado com sucesso!',
                    'notifications' => $notifications,
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar a assinatura do teste gratuito: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function paymentPix(Request $request)
    {
        try {
            DB::beginTransaction();
            $pix = $this->pixService->payment($request);

            if ($pix) {
                DB::commit();
                $notifications = NotificationsHelper::getNoRead($request->user()->id);

                return response()->json(['pix' => $pix, 'notifications' => $notifications], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao buscar QRCode e copia e cola do pix: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
