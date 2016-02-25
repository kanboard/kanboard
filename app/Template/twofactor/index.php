<div class="page-header">
    <h2><?= t('Two factor authentication') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('twofactor', $user['twofactor_activated'] == 1 ? 'deactivate' : 'show', array('user_id' => $user['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <p><?= t('Two-Factor Provider: ') ?><strong><?= $this->e($provider) ?></strong></p>
    <div class="form-actions">
        <?php if ($user['twofactor_activated'] == 1): ?>
            <input type="submit" value="<?= t('Disable two-factor authentication') ?>" class="btn btn-red"/>
        <?php else: ?>
            <input type="submit" value="<?= t('Enable two-factor authentication') ?>" class="btn btn-blue"/>
        <?php endif ?>
    </div>
</form>
