<?php

namespace Kanboard\Api\Authorization;

/**
 * Class CommentAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class CommentAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $comment_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->commentModel->getProjectId($comment_id));
        }
    }
}
