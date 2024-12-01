<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedController
{

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações para o feed: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
