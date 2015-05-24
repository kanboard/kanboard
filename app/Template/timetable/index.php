<div class="page-header">
    <h2><?= t('Timetable') ?></h2>
    <ul>
        <li><?= $this->url->link(t('Day timetable'), 'timetableday', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->url->link(t('Week timetable'), 'timetableweek', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->url->link(t('Time off timetable'), 'timetableoff', 'index', array('user_id' => $user['id'])) ?></li>
        <li><?= $this->url->link(t('Overtime timetable'), 'timetableextra', 'index', array('user_id' => $user['id'])) ?></li>
    </ul>
</div>

<form method="get" action="?" autocomplete="off" class="form-inline">

    <?= $this->form->hidden('controller', $values) ?>
    <?= $this->form->hidden('action', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->label(t('From'), 'from') ?>
    <?= $this->form->text('from', $values, array(), array(), 'form-date') ?>

    <?= $this->form->label(t('To'), 'to') ?>
    <?= $this->form->text('to', $values, array(), array(), 'form-date') ?>

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