<?php

namespace App\Services;

use App\Repositories\External\SettingExternalRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\FeedbacksSavedRepository;
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

        $user = $request->user();
        $message = $request->input('message');

        $data = $keySetting->value === '1'
            ? $this->prepareFullFeedbackData($user, $message)
            : $this->prepareBasicFeedbackData($user, $message);

        $keySetting->value === '1'
            ? $this->feedbackSavedRepository->create($data)
            : $this->repository->create($data);
    }

    protected function prepareFullFeedbackData($user, $message): array
    {
        $enterprise = $user->load('enterprise')->enterprise;

        return [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'enterprise_name' => $enterprise->name ?? 'N/A',
            'date_feedback' => now()->format('Y-m-d'),
            'message' => $message,
        ];
    }

    protected function prepareBasicFeedbackData($user, $message): array
    {
        return [
            'user_id' => $user->id,
            'enterprise_id' => $user->enterprise_id,
            'message' => $message,
        ];
    }
}
