<?php

namespace Kanboard\Api\Authorization;

/**
 * Class CategoryAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class CategoryAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $category_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->categoryModel->getProjectId($category_id));
        }
    }
}
