<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Tag Duplication
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TagDuplicationModel extends Base
{
    /**
     * Duplicate project tags to another project
     *
     * @access public
     * @param  integer $src_project_id
     * @param  integer $dst_project_id
     * @return bool
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $tags = $this->tagModel->getAllByProject($src_project_id);
        $results = array();

        foreach ($tags as $tag) {
            $results[] = $this->tagModel->create($dst_project_id, $tag['name'], $tag['color_id']);
        }

        return ! in_array(false, $results, true);
    }

    /**
     * Link tags to the new tasks
     *
     * @access public
     * @param  integer $src_task_id
     * @param  integer $dst_task_id
     * @param  integer $dst_project_id
     */
    public function duplicateTaskTagsToAnotherProject($src_task_id, $dst_task_id, $dst_project_id)
    {
        $tags = $this->taskTagModel->getTagsByTask($src_task_id);

        foreach ($tags as $tag) {
            $tag_id = $this->tagModel->getIdByName($dst_project_id, $tag['name']);

            if (empty($tag_id)) {
                $tag_id = $this->tagModel->create($dst_project_id, $tag['name'], $tag['color_id']);
            }

            $this->taskTagModel->associateTag($dst_task_id, $tag_id);
        }
    }

    /**
     * Duplicate tags to the new task
     *
     * @access public
     * @param  integer $src_task_id
     * @param  integer $dst_task_id
     */
    public function duplicateTaskTags($src_task_id, $dst_task_id)
    {
        $tags = $this->taskTagModel->getTagsByTask($src_task_id);

        foreach ($tags as $tag) {
            $this->taskTagModel->associateTag($dst_task_id, $tag['id']);
        }
    }

    /**
     * Sync tags that are not available in destination project
     *
     * @access public
     * @param  integer $task_id
     * @param  integer $dst_project_id
     */
    public function syncTaskTagsToAnotherProject($task_id, $dst_project_id)
    {
        $tags = $this->taskTagModel->getTagsByTaskNotAvailableInProject($task_id, $dst_project_id);

        foreach ($tags as $tag) {
            $tag_id = $this->tagModel->getIdByName($dst_project_id, $tag['name']);

            if (empty($tag_id)) {
                $tag_id = $this->tagModel->create($dst_project_id, $tag['name'], $tag['color_id']);
            }

            $this->taskTagModel->dissociateTag($task_id, $tag['id']);
            $this->taskTagModel->associateTag($task_id, $tag_id);
        }
    }
}
