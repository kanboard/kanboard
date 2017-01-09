<div class="page-header">
    <h2><?= t('Currency rates') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add or change currency rate'), 'CurrencyController', 'create') ?>
        </li>
        <li>
            <?= $this->modal->medium('edit', t('Change reference currency'), 'CurrencyController', 'change') ?>
        </li>
    </ul>
</div>

<div class="panel">
    <strong><?= t('Reference currency: %s', $application_currency) ?></strong>
</div>

<?php if (! empty($rates)): ?>
    <table class="table-striped">
        <tr>
            <th class="column-35"><?= t('Currency') ?></th>
            <th><?= t('Rate') ?></th>
        </tr>
        <?php foreach ($rates as $rate): ?>
        <tr>
            <td>
                <strong><?= $this->text->e($rate['currency']) ?></strong>
            </td>
            <td>
                <?= n($rate['rate']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
