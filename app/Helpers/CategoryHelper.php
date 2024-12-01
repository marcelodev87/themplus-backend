<?php

namespace App\Helpers;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryHelper
{
    public static function existsCategory($name, $type, $enterpriseId)
    {
        $categoryRepository = new CategoryRepository(new Category);

        $category = $categoryRepository->findByNameAndType($name, $type, $enterpriseId);
        if ($category) {
            throw new \Exception("Já existe uma categoria igual ou parecida para o tipo: {$type}");
        }
    }
}
