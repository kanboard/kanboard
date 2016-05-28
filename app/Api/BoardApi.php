<?php

namespace Kanboard\Api;

/**
 * Board API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class BoardApi extends BaseApi
{
    public function getBoard($project_id)
    {
        $this->checkProjectPermission($project_id);
        return $this->boardModel->getBoard($project_id);
    }
}
