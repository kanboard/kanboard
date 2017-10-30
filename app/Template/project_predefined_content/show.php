<div class="page-header">
    <h2><?= t('Predefined Contents') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ProjectPredefinedContentController', 'update', array('project_id' => $project['id'], 'redirect' => 'edit')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <fieldset>
        <legend><?= t('Predefined Email Subjects') ?></legend>
        <?= $this->form->textarea('predefined_email_subjects', $values, $errors, array('tabindex="1"')) ?>
        <p class="form-help"><?= t('Write one subject by line.') ?></p>
    </fieldset>

    <?= $this->modal->submitButtons(array('tabindex' => 2)) ?>
</form>
