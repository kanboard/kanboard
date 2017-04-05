<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('ConfigController', 'save', array('redirect' => 'integrations')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?php $contents = $this->hook->render('template:config:integrations', array('values' => $values)) ?>

    <?php if (empty($contents)): ?>
        <p class="alert"><?= t('There is no external integration installed.') ?></p>
    <?php else: ?>
        <?= $contents ?>
    <?php endif ?>
</form>
