<?php

namespace Kanboard\Api\Authorization;

use JsonRPC\Exception\AccessDeniedException;
use Kanboard\Core\Security\Role;

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
            $this->checkCommentAccess($comment_id);
        }
    }

    /**
     * @param $comment_id ID of the comment to check
     * @return void
     * @throws AccessDeniedException
     */
    protected function checkCommentAccess($comment_id)
    {
        if (empty($comment_id)) {
            throw new AccessDeniedException('Comment Not Found');
        }

        $Visibility = $this->commentModel->getVisibility($comment_id);
        $role = $this->userSession->getRole();

        if ($role === Role::APP_MANAGER && $Visibility === Role::APP_ADMIN) {
            throw new AccessDeniedException('Comment Access Denied');
        }

        if ($role === Role::APP_USER && $Visibility !== Role::APP_USER) {
            throw new AccessDeniedException('Comment Access Denied');
        }
    }
}
