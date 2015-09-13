<div class="page-header">
    <h2><?= t('Currency rates') ?></h2>
</div>

<?php if (! empty($rates)): ?>

<table class="table-stripped">
    <tr>
        <th class="column-35"><?= t('Currency') ?></th>
        <th><?= t('Rate') ?></th>
    </tr>
    <?php foreach ($rates as $rate): ?>
    <tr>
        <td>
            <strong><?= $this->e($rate['currency']) ?></strong>
        </td>
        <td>
            <?= n($rate['rate']) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<hr/>
<h3><?= t('Change reference currency') ?></h3>
<?php endif ?>
<form method="post" action="<?= $this->url->href('currency', 'reference') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Reference currency'), 'application_currency') ?>
    <?= $this->form->select('application_currency', $currencies, $config_values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<hr/>
<h3><?= t('Add a new currency rate') ?></h3>
<form method="post" action="<?= $this->url->href('currency', 'create') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Currency'), 'currency') ?>
    <?= $this->form->select('currency', $currencies, $values, $errors) ?><br/>

    <?= $this->form->label(t('Rate'), 'rate') ?>
    <?= $this->form->text('rate', $values, $errors, array(), 'form-numeric') ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
