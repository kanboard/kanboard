<div class="page-header">
    <h2><?= t('Week timetable') ?></h2>
</div>

<?php if (! empty($timetable)): ?>

<table class="table-fixed table-stripped">
    <tr>
        <th><?= t('Day') ?></th>
        <th><?= t('Start time') ?></th>
        <th><?= t('End time') ?></th>
        <th><?= t('Action') ?></th>
    </tr>
    <?php foreach ($timetable as $slot): ?>
    <tr>
        <td><?= $this->getWeekDay($slot['day']) ?></td>
        <td><?= $slot['start'] ?></td>
        <td><?= $slot['end'] ?></td>
        <td>
            <?= $this->a(t('Remove'), 'timetableweek', 'confirm', array('user_id' => $user['id'], 'slot_id' => $slot['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<h3><?= t('Add new time slot') ?></h3>
<?php endif ?>

<form method="post" action="<?= $this->u('timetableweek', 'save', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->formHidden('user_id', $values) ?>
    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Day'), 'day') ?>
    <?= $this->formSelect('day', $this->getWeekDays(), $values, $errors) ?>

    <?= $this->formLabel(t('Start time'), 'start') ?>
    <?= $this->formSelect('start', $this->getDayHours(), $values, $errors) ?>

    <?= $this->formLabel(t('End time'), 'end') ?>
    <?= $this->formSelect('end', $this->getDayHours(), $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>