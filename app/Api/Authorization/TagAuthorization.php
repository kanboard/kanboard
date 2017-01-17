<?php

namespace Kanboard\Api\Authorization;

/**
 * Class TagAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class TagAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $tag_id)
    {
        if ($this->userSession->isLogged()) {
            $tag = $this->tagModel->getById($tag_id);

            if (! empty($tag)) {
                $this->checkProjectPermission($class, $method, $tag['project_id']);
            }
        }
    }
}
