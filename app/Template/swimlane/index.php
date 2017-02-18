<div class="page-header">
    <h2><?= t('Swimlanes') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add a new swimlane'), 'SwimlaneController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>

<h3><?= t('Active swimlanes') ?></h3>

<?php if (empty($active_swimlanes)): ?>
    <p class="alert alert-error"><?= t('Your project must have at least one active swimlane.') ?></p>
<?php else: ?>
    <?= $this->render('swimlane/table', array(
        'swimlanes' => $active_swimlanes,
        'project'   => $project,
    )) ?>
<?php endif ?>

<?php if (! empty($inactive_swimlanes)): ?>
    <h3><?= t('Inactive swimlanes') ?></h3>
    <?= $this->render('swimlane/table', array(
        'swimlanes'       => $inactive_swimlanes,
        'project'         => $project,
        'disable_handle'  => true,
    )) ?>
<?php endif ?>
