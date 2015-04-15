<div class="page-header">
    <h2><?= t('Budget') ?></h2>
    <ul>
        <li><?= $this->a(t('Budget lines'), 'budget', 'create', array('project_id' => $project['id'])) ?></li>
        <li><?= $this->a(t('Cost breakdown'), 'budget', 'breakdown', array('project_id' => $project['id'])) ?></li>
    </ul>
</div>

<?php if (! empty($lines)): ?>
<table class="table-fixed table-stripped">
    <tr>
        <th class="column-20"><?= t('Budget line') ?></th>
        <th class="column-20"><?= t('Date') ?></th>
        <th><?= t('Comment') ?></th>
        <th><?= t('Action') ?></th>
    </tr>
    <?php foreach ($lines as $line): ?>
    <tr>
        <td><?= n($line['amount']) ?></td>
        <td><?= dt('%B %e, %Y', strtotime($line['date'])) ?></td>
        <td><?= $this->e($line['comment']) ?></td>
        <td>
            <?= $this->a(t('Remove'), 'budget', 'confirm', array('project_id' => $project['id'], 'budget_id' => $line['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<h3><?= t('New budget line') ?></h3>
<?php endif ?>

<form method="post" action="<?= $this->u('budget', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Amount'), 'amount') ?>
    <?= $this->formText('amount', $values, $errors, array('required'), 'form-numeric') ?>

    <?= $this->formLabel(t('Date'), 'date') ?>
    <?= $this->formText('date', $values, $errors, array('required'), 'form-date') ?>

    <?= $this->formLabel(t('Comment'), 'comment') ?>
    <?= $this->formText('comment', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>