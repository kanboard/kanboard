<div class="page-header">
    <h2><?= t('Add or change currency rate') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('CurrencyController', 'save') ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Currency'), 'currency') ?>
    <?= $this->form->select('currency', $currencies, $values, $errors) ?>
    <?= $this->form->label(t('Rate'), 'rate') ?>
    <?= $this->form->text('rate', $values, $errors, array('autofocus'), 'form-numeric') ?>
    <?= $this->modal->submitButtons() ?>
</form>
