<?php

namespace Kanboard\Api;

/**
 * Category API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Category extends \Kanboard\Core\Base
{
    public function getCategory($category_id)
    {
        return $this->category->getById($category_id);
    }

    public function getAllCategories($project_id)
    {
        return $this->category->getAll($project_id);
    }

    public function removeCategory($category_id)
    {
        return $this->category->remove($category_id);
    }

    public function createCategory($project_id, $name)
    {
        $values = array(
            'project_id' => $project_id,
            'name' => $name,
        );

        list($valid, ) = $this->category->validateCreation($values);
        return $valid ? $this->category->create($values) : false;
    }

    public function updateCategory($id, $name)
    {
        $values = array(
            'id' => $id,
            'name' => $name,
        );

        list($valid, ) = $this->category->validateModification($values);
        return $valid && $this->category->update($values);
    }
}
