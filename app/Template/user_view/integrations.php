<div class="page-header">
    <h2><?= t('Integrations') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('UserViewController', 'integrations', array('user_id' => $user['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?php $hooks = $this->hook->render('template:user:integrations', array('values' => $values)) ?>
    <?php if (! empty($hooks)): ?>
        <?= $hooks ?>
    <?php else: ?>
        <p class="alert"><?= t('No external integration registered.') ?></p>
    <?php endif ?>
</form>
