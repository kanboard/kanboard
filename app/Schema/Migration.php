<?php

namespace Schema;

use PDO;

function migrate_default_swimlane(PDO $pdo)
{
    $projects = get_all_projects($pdo);

    foreach ($projects as $project) {
        if (empty($project['default_swimlane'])) {
            $project['default_swimlane'] = 'Default swimlane';
        }

        $rq = $pdo->prepare('SELECT 1 FROM swimlanes WHERE name=? AND project_id=?');
        $rq->execute(array($project['default_swimlane'], $project['id']));

        if ($rq->fetchColumn()) {
            $project['default_swimlane'] = $project['default_swimlane'].' (Default swimlane)';
        }

        // Create new default swimlane
        $rq = $pdo->prepare('INSERT INTO swimlanes (project_id, name, is_active, position) VALUES (?, ?, ?, ?)');
        $rq->execute(array(
            $project['id'],
            $project['default_swimlane'],
            (int) $project['show_default_swimlane'],
            $project['show_default_swimlane'] == 1 ? 1 : 0,
        ));

        $swimlaneId = get_last_insert_id($pdo);

        // Reorder swimlanes if the default one was active
        if ($project['show_default_swimlane']) {
            $rq = $pdo->prepare("UPDATE swimlanes SET position=position+1 WHERE project_id=? AND is_active='1' AND id!=?");
            $rq->execute(array(
                $project['id'],
                $swimlaneId,
            ));
        }

        // Move all tasks to new swimlane
        $rq = $pdo->prepare("UPDATE tasks SET swimlane_id=? WHERE swimlane_id='0' AND project_id=?");
        $rq->execute(array(
            $swimlaneId,
            $project['id'],
        ));

        // Migrate automatic actions
        $rq = $pdo->prepare("SELECT action_has_params.id FROM action_has_params LEFT JOIN actions ON actions.id=action_has_params.action_id WHERE project_id=? AND name='swimlane_id' AND value='0'");
        $rq->execute(array($project['id']));
        $ids = $rq->fetchAll(PDO::FETCH_COLUMN, 0);

        $rq = $pdo->prepare("UPDATE action_has_params SET value=? WHERE id=?");

        foreach ($ids as $id) {
            $rq->execute(array($swimlaneId, $id));
        }
    }
}

function get_all_projects(PDO $pdo)
{
    $rq = $pdo->prepare('SELECT * FROM projects');
    $rq->execute();
    return $rq->fetchAll(PDO::FETCH_ASSOC);
}

function get_last_insert_id(PDO $pdo)
{
    if (DB_DRIVER === 'postgres') {
        $rq = $pdo->prepare('SELECT LASTVAL()');
        $rq->execute();
        return $rq->fetchColumn();
    }

    return $pdo->lastInsertId();
}

