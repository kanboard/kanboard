<?php

namespace Api;

/**
 * Project API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Project extends Base
{
    public function getProjectById($project_id)
    {
        return $this->formatProject($this->project->getById($project_id));
    }

    public function getProjectByName($name)
    {
        return $this->formatProject($this->project->getByName($name));
    }

    public function getAllProjects()
    {
        return $this->formatProjects($this->project->getAll());
    }

    public function removeProject($project_id)
    {
        return $this->project->remove($project_id);
    }

    public function enableProject($project_id)
    {
        return $this->project->enable($project_id);
    }

    public function disableProject($project_id)
    {
        return $this->project->disable($project_id);
    }

    public function enableProjectPublicAccess($project_id)
    {
        return $this->project->enablePublicAccess($project_id);
    }

    public function disableProjectPublicAccess($project_id)
    {
        return $this->project->disablePublicAccess($project_id);
    }

    public function getProjectActivities(array $project_ids)
    {
        return $this->projectActivity->getProjects($project_ids);
    }

    public function getProjectActivity($project_id)
    {
        return $this->projectActivity->getProject($project_id);
    }

    public function createProject($name, $description = null)
    {
        $values = array(
            'name' => $name,
            'description' => $description
        );

        list($valid,) = $this->project->validateCreation($values);
        return $valid ? $this->project->create($values) : false;
    }

    public function updateProject($id, $name, $description = null)
    {
        $values = array(
            'id' => $id,
            'name' => $name,
            'description' => $description
        );

        list($valid,) = $this->project->validateModification($values);
        return $valid && $this->project->update($values);
    }

    private function formatProject($project)
    {
        if (! empty($project)) {
            $project['url'] = array(
                'board' => $this->helper->url->to('board', 'show', array('project_id' => $project['id']), '', true),
                'calendar' => $this->helper->url->to('calendar', 'show', array('project_id' => $project['id']), '', true),
                'list' => $this->helper->url->to('listing', 'show', array('project_id' => $project['id']), '', true),
            );
        }

        return $project;
    }

    private function formatProjects($projects)
    {
        if (! empty($projects)) {
            foreach ($projects as &$project) {
                $project = $this->formatProject($project);
            }
        }

        return $projects;
    }
}
