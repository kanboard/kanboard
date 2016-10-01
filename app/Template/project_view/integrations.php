<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('ProjectViewController', 'updateIntegrations', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?php $integrations = $this->hook->render('template:project:integrations', array('project' => $project, 'values' => $values, 'webhook_token' => $webhook_token)) ?>

    <?php if (empty($integrations)): ?>
        <p class="alert"><?= t('There is no integration registered at the moment.') ?></p>
    <?php else: ?>
        <?= $integrations ?>
    <?php endif ?>
</form>
