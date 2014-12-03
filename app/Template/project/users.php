<div class="page-header">
    <h2><?= t('List of authorized users') ?></h2>
</div>

<?php if ($project['is_everybody_allowed']): ?>
    <div class="alert alert-info"><?= t('Everybody have access to this project.') ?></div>
<?php else: ?>

    <?php if (empty($users['allowed'])): ?>
        <div class="alert alert-error"><?= t('Nobody have access to this project.') ?></div>
    <?php else: ?>
    <div class="alert alert-info">
        <p><?= t('Only those users have access to this project:') ?></p>
        <ul>
        <?php foreach ($users['allowed'] as $user_id => $username): ?>
            <li>
                <strong><?= Helper\escape($username) ?></strong>
                <?php if ($project['is_private'] == 0): ?>
                    (<?= Helper\a(t('revoke'), 'project', 'revoke', array('project_id' => $project['id'], 'user_id' => $user_id), true) ?>)
                <?php endif ?>
            </li>
        <?php endforeach ?>
        </ul>
        <p><?= t('Don\'t forget that administrators have access to everything.') ?></p>
    </div>
    <?php endif ?>

    <?php if ($project['is_private'] == 0 && ! empty($users['not_allowed'])): ?>
        <hr/>
        <form method="post" action="<?= Helper\u('project', 'allow', array('project_id' => $project['id'])) ?>" autocomplete="off">

            <?= Helper\form_csrf() ?>

            <?= Helper\form_hidden('project_id', array('project_id' => $project['id'])) ?>

            <?= Helper\form_label(t('User'), 'user_id') ?>
            <?= Helper\form_select('user_id', $users['not_allowed']) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Allow this user') ?>" class="btn btn-blue"/>
            </div>
        </form>
    <?php endif ?>

<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
<hr/>
<form method="post" action="<?= Helper\u('project', 'allowEverybody', array('project_id' => $project['id'])) ?>">
    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', array('id' => $project['id'])) ?>
    <?= Helper\form_checkbox('is_everybody_allowed', t('Allow everybody to access to this project'), 1, $project['is_everybody_allowed']) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
<?php endif ?>
