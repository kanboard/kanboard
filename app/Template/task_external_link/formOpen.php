<div class="page-header">
    <h2><?= t('Add a new external link') ?></h2>
</div>

<form id="ival-<?= $values['index'] ?>" action="<?= $this->url->href('TaskExternalLinkController', 'save', array('task_id' => $task['id'])) ?>" method="post" autocomplete="off">


