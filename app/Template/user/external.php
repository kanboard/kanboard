<div class="page-header">
    <h2><?= t('External authentications') ?></h2>
</div>

<?php $html = $this->hook->render('template:user:external', array('user' => $user)) ?>

<?php if (empty($html)): ?>
    <p class="alert"><?= t('No external authentication enabled.') ?></p>
<?php else: ?>
    <?= $html ?>
<?php endif ?>
