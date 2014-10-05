<div class="page-header">
    <h2><?= t('List of authorized users') ?></h2>
</div>

<?php if (empty($users['allowed'])): ?>
    <div class="alert alert-info"><?= t('Nobody have access to this project.') ?></div>
<?php else: ?>
<div class="listing">
    <p><?= t('Only those users have access to this project:') ?></p>
    <ul>
    <?php foreach ($users['allowed'] as $user_id => $username): ?>
        <li>
            <strong><?= Helper\escape($username) ?></strong>
            <?php if ($project['is_private'] == 0): ?>
                (<a href="?controller=project&amp;action=revoke&amp;project_id=<?= $project['id'] ?>&amp;user_id=<?= $user_id.Helper\param_csrf() ?>"><?= t('revoke') ?></a>)
            <?php endif ?>
        </li>
    <?php endforeach ?>
    </ul>
    <p><?= t('Don\'t forget that administrators have access to everything.') ?></p>
</div>
<?php endif ?>

<?php if ($project['is_private'] == 0 && ! empty($users['not_allowed'])): ?>
    <form method="post" action="?controller=project&amp;action=allow&amp;project_id=<?= $project['id'] ?>" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_hidden('project_id', array('project_id' => $project['id'])) ?>

        <?= Helper\form_label(t('User'), 'user_id') ?>
        <?= Helper\form_select('user_id', $users['not_allowed']) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Allow this user') ?>" class="btn btn-blue"/>
        </div>
    </form>
<?php endif ?>