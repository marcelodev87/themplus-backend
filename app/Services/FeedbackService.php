<?php

namespace App\Services;

use App\Repositories\External\SettingExternalRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\FeedbacksSavedRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeedbackService
{
    private $repository;

    private $settingExternalRepository;

    private $feedbackSavedRepository;

    public function __construct(
        FeedbackRepository $repository,
        SettingExternalRepository $settingExternalRepository,
        FeedbacksSavedRepository $feedbackSavedRepository
    ) {
        $this->repository = $repository;
        $this->settingExternalRepository = $settingExternalRepository;
        $this->feedbackSavedRepository = $feedbackSavedRepository;
    }

    public function saveFeedbackBySettings(Request $request)
    {
        $keySetting = $this->settingExternalRepository->getSettingKey('allow_feedback_saved');

        if ($keySetting->value === '1') {
            $data = [
                'user_name' => $request->user()->name,
                'user_email' => $request->user()->email,
                'enterprise_name' => $request->user()->load('enterprise')->name,
                'date_feedback' => Carbon::now()->format('Y-m-d'),
                'message' => $request->input('message'),
            ];
            $feedbackSaved = $this->feedbackSavedRepository->create($data);
        }

        if ($keySetting->value === '0') {
            $data = [
                'user_id' => $request->user()->id,
                'enterprise_id' => $request->user()->enterprise_id,
                'message' => $request->input('message'),
            ];

            $feedbackSaved = $this->repository->create($data);
        }

        if (! $feedbackSaved) {
            throw new \Exception('Falha ao enviar mensagem');
        }

        return $feedbackSaved;
    }
}
