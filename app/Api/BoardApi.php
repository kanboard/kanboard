<?php

namespace Kanboard\Api;

use Kanboard\Formatter\BoardFormatter;

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

        return BoardFormatter::getInstance($this->container)
            ->withProjectId($project_id)
            ->withQuery($this->taskFinderModel->getExtendedQuery())
            ->format();
    }
}
