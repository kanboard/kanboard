<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\CategoryAuthorization;
use Kanboard\Api\Authorization\ProjectAuthorization;

/**
 * Category API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class CategoryProcedure extends BaseProcedure
{
    public function getCategory($category_id)
    {
        CategoryAuthorization::getInstance($this->container)->check($this->getClassName(), 'getCategory', $category_id);
        return $this->categoryModel->getById($category_id);
    }

    public function getAllCategories($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllCategories', $project_id);
        return $this->categoryModel->getAll($project_id);
    }

    public function removeCategory($category_id)
    {
        CategoryAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeCategory', $category_id);
        return $this->categoryModel->remove($category_id);
    }

    public function createCategory($project_id, $name)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createCategory', $project_id);

        $values = array(
            'project_id' => $project_id,
            'name' => $name,
        );

        list($valid, ) = $this->categoryValidator->validateCreation($values);
        return $valid ? $this->categoryModel->create($values) : false;
    }

    public function updateCategory($id, $name)
    {
        CategoryAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateCategory', $id);

        $values = array(
            'id' => $id,
            'name' => $name,
        );

        list($valid, ) = $this->categoryValidator->validateModification($values);
        return $valid && $this->categoryModel->update($values);
    }
}
