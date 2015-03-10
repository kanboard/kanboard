<div class="page-header">
    <h2><?= t('Timetable') ?></h2>
    <ul>
        <li><?= $this->a(t('Day timetable'), 'timetableday', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->a(t('Week timetable'), 'timetableweek', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->a(t('Time off timetable'), 'timetableoff', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->a(t('Overtime timetable'), 'timetableextra', 'index', array('user_id' => $user['id'])) ?></li>
    </ul>
</div>

<form method="get" action="?" autocomplete="off" class="form-inline">

    <?= $this->formHidden('controller', $values) ?>
    <?= $this->formHidden('action', $values) ?>
    <?= $this->formHidden('user_id', $values) ?>

    <?= $this->formLabel(t('From'), 'from') ?>
    <?= $this->formText('from', $values, array(), array(), 'form-date') ?>

    <?= $this->formLabel(t('To'), 'to') ?>
    <?= $this->formText('to', $values, array(), array(), 'form-date') ?>

    <input type="submit" value="<?= t('Execute') ?>" class="btn btn-blue"/>
</form>

<?php if (! empty($timetable)): ?>
<hr/>
<h3><?= t('Work timetable') ?></h3>
<table class="table-fixed table-stripped">
    <tr>
        <th><?= t('Day') ?></th>
        <th><?= t('Start') ?></th>
        <th><?= t('End') ?></th>
    </tr>
    <?php foreach ($timetable as $slot): ?>
    <tr>
        <td><?= dt('%B %e, %Y', $slot[0]->getTimestamp()) ?></td>
        <td><?= dt('%k:%M %p', $slot[0]->getTimestamp()) ?></td>
        <td><?= dt('%k:%M %p', $slot[1]->getTimestamp()) ?></td>
    </tr>
    <?php endforeach ?>
</table>

<?php endif ?>