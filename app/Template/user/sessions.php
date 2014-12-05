<div class="page-header">
    <h2><?= t('Persistent connections') ?></h2>
</div>

<?php if (empty($sessions)): ?>
    <p class="alert"><?= t('No session.') ?></p>
<?php else: ?>
    <table class="table-small">
    <tr>
        <th><?= t('Creation date') ?></th>
        <th><?= t('Expiration date') ?></th>
        <th><?= t('IP address') ?></th>
        <th><?= t('User agent') ?></th>
        <th><?= t('Action') ?></th>
    </tr>
    <?php foreach($sessions as $session): ?>
    <tr>
        <td><?= dt('%B %e, %Y at %k:%M %p', $session['date_creation']) ?></td>
        <td><?= dt('%B %e, %Y at %k:%M %p', $session['expiration']) ?></td>
        <td><?= Helper\escape($session['ip']) ?></td>
        <td><?= Helper\escape(Helper\summary($session['user_agent'])) ?></td>
        <td><?= Helper\a(t('Remove'), 'user', 'removeSession', array('user_id' => $user['id'], 'id' => $session['id']), true) ?></td>
    </tr>
    <?php endforeach ?>
    </table>
<?php endif ?>
