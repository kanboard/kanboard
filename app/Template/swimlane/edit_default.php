<div class="page-header">
    <h2><?= t('Change default swimlane') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('SwimlaneController', 'updateDefault', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Name'), 'default_swimlane') ?>
    <?= $this->form->text('default_swimlane', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->form->checkbox('show_default_swimlane', t('Show default swimlane'), 1, $values['show_default_swimlane'] == 1) ?>

    <?= $this->modal->submitButtons() ?>
</form>
