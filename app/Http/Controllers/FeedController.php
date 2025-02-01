<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedController
{
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json(['filled_data' => $filledData, 'notifications' => $notifications], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações para o feed: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
