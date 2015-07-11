<div class="page-header">
    <h2><?= t('Time off timetable') ?></h2>
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
            <?= $this->url->link(t('Remove'), 'timetableoff', 'confirm', array('user_id' => $user['id'], 'slot_id' => $slot['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?= $paginator ?>

<?php endif ?>

<form method="post" action="<?= $this->url->href('timetableoff', 'save', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->hidden('user_id', $values) ?>
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Day'), 'date') ?>
    <?= $this->form->text('date', $values, $errors, array('required'), 'form-date') ?>

    <?= $this->form->checkbox('all_day', t('All day'), 1) ?>

    <?= $this->form->label(t('Start time'), 'start') ?>
    <?= $this->form->select('start', $this->dt->getDayHours(), $values, $errors) ?>

    <?= $this->form->label(t('End time'), 'end') ?>
    <?= $this->form->select('end', $this->dt->getDayHours(), $values, $errors) ?>

    <?= $this->form->label(t('Comment'), 'comment') ?>
    <?= $this->form->text('comment', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>