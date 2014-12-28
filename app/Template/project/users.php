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
                <strong><?= $this->e($username) ?></strong>
                <?php $is_owner = array_key_exists($user_id, $users['owners']);
                      if ($is_owner): ?> [owner] <?php endif ?>
                <?php if ($project['is_private'] == 0): ?>
                    <?php if ($is_owner): ?>
                        (<a href=<?= $this->u('project', 'setOwner', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 0), true) ?> ><?= t('set user') ?></a>
                    <?php else: ?>
                        (<a href=<?= $this->u('project', 'setOwner', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 1), true) ?> ><?= t('set manager') ?></a>
                    <?php endif ?>
                    or
                    <?= $this->a(t('revoke'), 'project', 'revoke', array('project_id' => $project['id'], 'user_id' => $user_id), true) ?>)
                <?php endif ?>
            </li>
        <?php endforeach ?>
        </ul>
        <p><?= t('Don\'t forget that administrators have access to everything.') ?></p>
    </div>
    <?php endif ?>

    <?php if ($project['is_private'] == 0 && ! empty($users['not_allowed'])): ?>
        <hr/>
        <form method="post" action="<?= $this->u('project', 'allow', array('project_id' => $project['id'])) ?>" autocomplete="off">

            <?= $this->formCsrf() ?>

            <?= $this->formHidden('project_id', array('project_id' => $project['id'])) ?>

            <?= $this->formLabel(t('User'), 'user_id') ?>
            <?= $this->formSelect('user_id', $users['not_allowed']) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Allow this user') ?>" class="btn btn-blue"/>
            </div>
        </form>
    <?php endif ?>

<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
<hr/>
<form method="post" action="<?= $this->u('project', 'allowEverybody', array('project_id' => $project['id'])) ?>">
    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', array('id' => $project['id'])) ?>
    <?= $this->formCheckbox('is_everybody_allowed', t('Allow everybody to access to this project'), 1, $project['is_everybody_allowed']) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
<?php endif ?>
