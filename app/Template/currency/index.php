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
<form method="post" action="<?= $this->u('currency', 'reference') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Reference currency'), 'application_currency') ?>
    <?= $this->formSelect('application_currency', $currencies, $config_values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<hr/>
<h3><?= t('Add a new currency rate') ?></h3>
<form method="post" action="<?= $this->u('currency', 'create') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Currency'), 'currency') ?>
    <?= $this->formSelect('currency', $currencies, $values, $errors) ?><br/>

    <?= $this->formLabel(t('Rate'), 'rate') ?>
    <?= $this->formText('rate', $values, $errors, array(), 'form-numeric') ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<p class="alert alert-info"><?= t('Currency rates are used to calculate project budget.') ?></p>
