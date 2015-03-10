<div class="page-header">
    <h2><?= t('Overtime timetable') ?></h2>
</div>

<?php if (! $paginator->isEmpty()): ?>

<table class="table-fixed table-stripped">
    <tr>
        <th><?= $paginator->order(t('Day'), 'Day') ?></th>
        <th><?= $paginator->order(t('All day'), 'all_day') ?></th>
        <th><?= $paginator->order(t('Start time'), 'start') ?></th>
        <th><?= $paginator->order(t('End time'), 'end') ?></th>
        <th class="column-40"><?= t('Comment') ?></th>
        <th><?= t('Action') ?></th>
    </tr>
    <?php foreach ($paginator->getCollection() as $slot): ?>
    <tr>
        <td><?= $slot['date'] ?></td>
        <td><?= $slot['all_day'] == 1 ? t('Yes') : t('No') ?></td>
        <td><?= $slot['start'] ?></td>
        <td><?= $slot['end'] ?></td>
        <td><?= $this->e($slot['comment']) ?></td>
        <td>
            <?= $this->a(t('Remove'), 'timetableextra', 'confirm', array('user_id' => $user['id'], 'slot_id' => $slot['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?= $paginator ?>

<?php endif ?>

<form method="post" action="<?= $this->u('timetableextra', 'save', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->formHidden('user_id', $values) ?>
    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Day'), 'date') ?>
    <?= $this->formText('date', $values, $errors, array('required'), 'form-date') ?>

    <?= $this->formCheckbox('all_day', t('All day'), 1) ?>

    <?= $this->formLabel(t('Start time'), 'start') ?>
    <?= $this->formSelect('start', $this->getDayHours(), $values, $errors) ?>

    <?= $this->formLabel(t('End time'), 'end') ?>
    <?= $this->formSelect('end', $this->getDayHours(), $values, $errors) ?>

    <?= $this->formLabel(t('Comment'), 'comment') ?>
    <?= $this->formText('comment', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>