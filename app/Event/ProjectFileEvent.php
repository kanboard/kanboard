<?php

namespace Kanboard\Event;

class ProjectFileEvent extends GenericEvent
{
    public function getProjectId()
    {
        if (isset($this->container['file']['project_id'])) {
            return $this->container['file']['project_id'];
        }

        return null;
    }
}
