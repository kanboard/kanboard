<div class="page-header">
    <h2><?= t('Persistent connections') ?></h2>
</div>

<?php if (empty($sessions)): ?>
    <p class="alert"><?= t('No session.') ?></p>
<?php else: ?>
    <table class="table-small table-fixed table-scrolling table-striped">
    <tr>
        <th class="column-20"><?= t('Creation date') ?></th>
        <th class="column-20"><?= t('Expiration date') ?></th>
        <th class="column-15"><?= t('IP address') ?></th>
        <th><?= t('User agent') ?></th>
        <th class="column-10"><?= t('Action') ?></th>
    </tr>
    <?php foreach ($sessions as $session): ?>
    <tr>
        <td><?= $this->dt->datetime($session['date_creation']) ?></td>
        <td><?= $this->dt->datetime($session['expiration']) ?></td>
        <td><?= $this->text->e($session['ip']) ?></td>
        <td><?= $this->text->e($session['user_agent']) ?></td>
        <td><?= $this->url->link(t('Remove'), 'UserViewController', 'removeSession', array('user_id' => $user['id'], 'id' => $session['id']), true, 'js-modal-replace') ?></td>
    </tr>
    <?php endforeach ?>
    </table>
<?php endif ?>
