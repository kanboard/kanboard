<div class="page-header">
    <h2><?= t('Edit external link') ?></h2>
</div>

<form action="<?= $this->url->href('TaskExternalLinkController', 'update', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>" method="post" autocomplete="off">
    <?= $this->render('task_external_link/form', array('task' => $task, 'dependencies' => $dependencies, 'values' => $values, 'errors' => $errors)) ?>
    <?= $this->modal->submitButtons() ?>
</form>
