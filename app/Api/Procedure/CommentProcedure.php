<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\CommentAuthorization;
use Kanboard\Api\Authorization\TaskAuthorization;

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
        return $this->commentModel->getAll($task_id);
    }

    public function removeComment($comment_id)
    {
        CommentAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeComment', $comment_id);
        return $this->commentModel->remove($comment_id);
    }

    public function createComment($task_id, $user_id, $content, $reference = '')
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'createComment', $task_id);
        
        $values = array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            'comment' => $content,
            'reference' => $reference,
        );

        list($valid, ) = $this->commentValidator->validateCreation($values);

        return $valid ? $this->commentModel->create($values) : false;
    }

    public function updateComment($id, $content)
    {
        CommentAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateComment', $id);
        
        $values = array(
            'id' => $id,
            'comment' => $content,
        );

        list($valid, ) = $this->commentValidator->validateModification($values);
        return $valid && $this->commentModel->update($values);
    }
}
