<div class="page-header">
    <h2><?= t('Add link label') ?></h2>
</div>

<form action="<?= $this->url->href('LinkController', 'save') ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Label'), 'label') ?>
    <?= $this->form->text('label', $values, $errors, array('required', 'autofocus')) ?>
    <?= $this->form->label(t('Opposite label'), 'opposite_label') ?>
    <?= $this->form->text('opposite_label', $values, $errors) ?>
    <?= $this->modal->submitButtons() ?>
</form>
