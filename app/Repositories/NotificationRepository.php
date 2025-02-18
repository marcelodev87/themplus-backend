<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    protected $model;

    protected $userRepository;

    public function __construct(Notification $notification, UserRepository $userRepository)
    {
        $this->model = $notification;
        $this->userRepository = $userRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getInbox($userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create($entepriseId, $title, $text)
    {
        $users = $this->userRepository->getAllByEnterprise($entepriseId);

        foreach ($users as $user) {
            $data = [
                'user_id' => $user->id,
                'enterprise_id' => $entepriseId,
                'title' => $title,
                'text' => $text,
            ];

            $this->model->create($data);
        }
    }

    public function createForUser($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $notification = $this->findById($id);
        if ($notification) {
            $notification->update($data);

            return $notification;
        }

        return null;
    }

    public function updateAll($userId)
    {
        $updated = $this->model->where('user_id', $userId)->update(['read' => 1]);

        return $updated;
    }

    public function delete($id)
    {
        $notification = $this->findById($id);
        if ($notification) {
            return $notification->delete();
        }

        return false;
    }
}
