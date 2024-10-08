<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\CommentAuthorization;
use Kanboard\Api\Authorization\TaskAuthorization;
use Kanboard\Core\Security\Role;

/**
 * Comment API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class CommentProcedure extends BaseProcedure
{
    public function getComment($comment_id)
    {
        CommentAuthorization::getInstance($this->container)->check($this->getClassName(), 'getComment', $comment_id);
        return $this->commentModel->getById($comment_id);
    }

    public function getAllComments($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllComments', $task_id);
        $comments = $this->commentModel->getAll($task_id);
        $filteredComments = [];
        $userRole = $this->userSession->getRole();

        foreach ($comments as $comment) {
            if ($userRole === Role::APP_MANAGER && $comment['visibility'] === Role::APP_ADMIN) {
                continue;
            }

            if ($userRole === Role::APP_USER && $comment['visibility'] !== Role::APP_USER) {
                continue;
            }

            $filteredComments[] = $comment;
        }

        return $filteredComments;
    }

    public function removeComment($comment_id)
    {
        CommentAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeComment', $comment_id);

        if ($this->userSession->isLogged()) {
            $loggedUserID = $this->userSession->getId();
            $comment = $this->commentModel->getById($comment_id);
            if ($comment['user_id'] != $loggedUserID) {
                return false;
            }
        }

        return $this->commentModel->remove($comment_id);
    }

    public function createComment($task_id, $user_id, $content, $reference = '', $visibility = Role::APP_USER)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'createComment', $task_id);

        if ($this->userSession->isLogged()) {
            $loggedUserID = $this->userSession->getId();
            if ($user_id != $loggedUserID) {
                return false;
            }

            $userRole = $this->userSession->getRole();
            if ($userRole === Role::APP_MANAGER && $visibility === Role::APP_ADMIN) {
                return false;
            }

            if ($userRole === Role::APP_USER && $visibility !== Role::APP_USER) {
                return false;
            }
        }

        $values = array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            'comment' => $content,
            'reference' => $reference,
            'visibility' => $visibility,
        );

        list($valid, ) = $this->commentValidator->validateCreation($values);

        return $valid ? $this->commentModel->create($values) : false;
    }

    public function updateComment($id, $content)
    {
        CommentAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateComment', $id);

        if ($this->userSession->isLogged()) {
            $loggedUserID = $this->userSession->getId();
            $comment = $this->commentModel->getById($id);
            if ($comment['user_id'] != $loggedUserID) {
                return false;
            }
        }

        $values = array(
            'id' => $id,
            'comment' => $content,
        );

        list($valid, ) = $this->commentValidator->validateModification($values);
        return $valid && $this->commentModel->update($values);
    }
}
