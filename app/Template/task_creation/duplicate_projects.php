<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= $this->text->e($task['title']) ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('There is no destination project available.') ?></p>
    <div class="form-actions">
        <?= $this->url->link(t('cancel'), 'BoardViewController', 'show', array('project_id' => $task['project_id']), false, 'js-modal-close btn') ?>
    </div>
<?php else: ?>
    <form method="post" action="<?= $this->url->href('TaskCreationController', 'duplicateProjects', array('project_id' => $task['project_id'])) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('task_id', $values) ?>

        <?= $this->form->select(
            'project_ids[]',
            $projects_list,
            $values,
            array(),
            array('multiple')
        ) ?>

        <?= $this->modal->submitButtons() ?>
    </form>
<?php endif ?>
