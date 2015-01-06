<div class="page-header">
    <h2><?= t('List of authorized users') ?></h2>
</div>

<?php if ($project['is_everybody_allowed']): ?>
    <div class="alert"><?= t('Everybody have access to this project.') ?></div>
<?php else: ?>

    <?php if (empty($users['allowed'])): ?>
        <div class="alert alert-error"><?= t('Nobody have access to this project.') ?></div>
    <?php else: ?>
        <table>
            <tr>
                <th><?= t('User') ?></th>
                <th><?= t('Role for this project') ?></th>
                <?php if ($project['is_private'] == 0): ?>
                    <th><?= t('Actions') ?></th>
                <?php endif ?>
            </tr>
            <?php foreach ($users['allowed'] as $user_id => $username): ?>
            <tr>
                <td><?= $this->e($username) ?></td>
                <td><?= isset($users['managers'][$user_id]) ? t('Project manager') : t('Project member') ?></td>
                <?php if ($project['is_private'] == 0): ?>
                <td>
                    <ul>
                        <li><?= $this->a(t('Revoke'), 'project', 'revoke', array('project_id' => $project['id'], 'user_id' => $user_id), true) ?></li>
                        <li>
                            <?php if (isset($users['managers'][$user_id])): ?>
                                <?= $this->a(t('Set project member'), 'project', 'role', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 0), true) ?>
                            <?php else: ?>
                                <?= $this->a(t('Set project manager'), 'project', 'role', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 1), true) ?>
                            <?php endif ?>
                        </li>
                    </ul>
                </td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </table>
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

<div class="alert alert-info">
    <ul>
        <li><?= t('A project manager can change the settings of the project and have more privileges than a standard user.') ?></li>
        <li><?= t('Don\'t forget that administrators have access to everything.') ?></li>
    </ul>
</div>
