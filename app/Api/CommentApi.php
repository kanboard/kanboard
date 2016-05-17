<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;

/**
 * Comment API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class CommentApi extends Base
{
    public function getComment($comment_id)
    {
        return $this->comment->getById($comment_id);
    }

    public function getAllComments($task_id)
    {
        return $this->comment->getAll($task_id);
    }

    public function removeComment($comment_id)
    {
        return $this->comment->remove($comment_id);
    }

    public function createComment($task_id, $user_id, $content, $reference = '')
    {
        $values = array(
            'task_id' => $task_id,
            'user_id' => $user_id,
            'comment' => $content,
            'reference' => $reference,
        );

        list($valid, ) = $this->commentValidator->validateCreation($values);

        return $valid ? $this->comment->create($values) : false;
    }

    public function updateComment($id, $content)
    {
        $values = array(
            'id' => $id,
            'comment' => $content,
        );

        list($valid, ) = $this->commentValidator->validateModification($values);
        return $valid && $this->comment->update($values);
    }
}
