<?php

namespace Kanboard\Api;

/**
 * Board API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Board extends Base
{
    public function getBoard($project_id)
    {
        $this->checkProjectPermission($project_id);
        return $this->board->getBoard($project_id);
    }
}
