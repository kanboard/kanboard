<div class="page-header">
    <h2><?= t('Budget lines') ?></h2>
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
            <?= $this->url->link(t('Remove'), 'budget', 'confirm', array('project_id' => $project['id'], 'budget_id' => $line['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<h3><?= t('New budget line') ?></h3>
<?php endif ?>

<form method="post" action="<?= $this->url->href('budget', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Amount'), 'amount') ?>
    <?= $this->form->text('amount', $values, $errors, array('required'), 'form-numeric') ?>

    <?= $this->form->label(t('Date'), 'date') ?>
    <?= $this->form->text('date', $values, $errors, array('required'), 'form-date') ?>

    <?= $this->form->label(t('Comment'), 'comment') ?>
    <?= $this->form->text('comment', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>