<?php

namespace App\Http\Controllers;

use App\Repositories\SubscriptionRepository;
use App\Services\SubscriptionService;
use App\Services\CreditCardService;
use App\Services\PixService;
use App\Helpers\NotificationsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController
{
    protected $repository;
    protected $creditCardService;
    protected $pixService;
    protected $service;

     public function __construct(SubscriptionRepository $repository, CreditCardService $creditCardService, PixService $pixService, SubscriptionService $service)
    {
        $this->repository = $repository;
        $this->creditCardService = $creditCardService;
        $this->pixService = $pixService;
        $this->service = $service;
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
            $result = $this->creditCardService->payment($request);

            if($result){
                $notifications = NotificationsHelper::getNoRead($request->user()->id);
                return response()->json(['result' => $result, 'notifications' => $notifications], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao fazer pagamento com cartão de crédito: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

        public function paymentPix(Request $request)
    {
        try {
            $pix = $this->pixService->payment($request);

            if($pix){
                $notifications = NotificationsHelper::getNoRead($request->user()->id);
                return response()->json(['pix' => $pix, 'notifications' => $notifications], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao buscar QRCode e copia e cola do pix: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
        public function updateFreeSubscription(Request $request)
    {
        try {
            DB::beginTransaction();
            $result = $this->service->updateForFreeSubscription($request);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            if($result){
                DB::commit();
                return response()->json(['notifications' => $notifications], 200);
            } else {
                return response()->json(['notifications' => $notifications], 204);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro ao atualizar assinatura para a assinatura grátis: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
