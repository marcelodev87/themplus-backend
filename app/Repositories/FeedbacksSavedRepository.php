<?php

namespace App\Repositories;

use App\Models\FeedbackSaved;

class FeedbacksSavedRepository
{
    protected $model;

    public function __construct(FeedbackSaved $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete($id)
    {
        $feedback = $this->findById($id);
        if ($feedback) {
            return $feedback->delete();
        }

        return false;
    }
}
