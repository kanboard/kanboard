<div class="page-header">
    <h2><?= t('Email settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ConfigController', 'save', array('redirect' => 'email')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <legend><?= t('Outgoing Emails') ?></legend>
        <?php if (MAIL_CONFIGURATION): ?>
            <?= $this->form->label(t('Email sender address'), 'mail_sender_address') ?>
            <?= $this->form->text('mail_sender_address', $values, $errors, array('placeholder="'.MAIL_FROM.'"')) ?>

            <?= $this->form->label(t('Email transport'), 'mail_transport') ?>
            <?= $this->form->select('mail_transport', $mail_transports, $values, $errors) ?>
        <?php else: ?>
            <p class="alert"><?= t('The email configuration has been disabled by the administrator.') ?></p>
        <?php endif ?>
    </fieldset>

    <?= $this->hook->render('template:config:email', array('values' => $values, 'errors' => $errors)) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
