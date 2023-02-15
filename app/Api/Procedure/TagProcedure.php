<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Api\Authorization\TagAuthorization;

/**
 * Class TagProcedure
 *
 * @package Kanboard\Api\Procedure
 * @author  Frederic Guillot
 */
class TagProcedure extends BaseProcedure
{
    public function getAllTags()
    {
        return $this->tagModel->getAll();
    }

    public function getTagsByProject($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTagsByProject', $project_id);
        return $this->tagModel->getAllByProject($project_id);
    }

    public function createTag($project_id, $tag, $color_id = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createTag', $project_id);
        return $this->tagModel->create($project_id, $tag, $color_id);
    }

    public function updateTag($tag_id, $tag, $color_id = null)
    {
        TagAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateTag', $tag_id);
        return $this->tagModel->update($tag_id, $tag, $color_id);
    }

    public function removeTag($tag_id)
    {
        TagAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeTag', $tag_id);
        return $this->tagModel->remove($tag_id);
    }
}
