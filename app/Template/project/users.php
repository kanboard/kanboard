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
                        <li><?= $this->url->link(t('Revoke'), 'project', 'revoke', array('project_id' => $project['id'], 'user_id' => $user_id), true) ?></li>
                        <li>
                            <?php if (isset($users['managers'][$user_id])): ?>
                                <?= $this->url->link(t('Set project member'), 'project', 'role', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 0), true) ?>
                            <?php else: ?>
                                <?= $this->url->link(t('Set project manager'), 'project', 'role', array('project_id' => $project['id'], 'user_id' => $user_id, 'is_owner' => 1), true) ?>
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
        <form method="post" action="<?= $this->url->href('project', 'allow', array('project_id' => $project['id'])) ?>" autocomplete="off">

            <?= $this->form->csrf() ?>

            <?= $this->form->hidden('project_id', array('project_id' => $project['id'])) ?>

            <?= $this->form->label(t('User'), 'user_id') ?>
            <?= $this->form->select('user_id', $users['not_allowed'], array(), array(), array('data-notfound="'.t('No results match:').'"'), 'chosen-select') ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Allow this user') ?>" class="btn btn-blue"/>
            </div>
        </form>
    <?php endif ?>

<?php endif ?>

<?php if ($project['is_private'] == 0): ?>
<hr/>
<form method="post" action="<?= $this->url->href('project', 'allowEverybody', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', array('id' => $project['id'])) ?>
    <?= $this->form->checkbox('is_everybody_allowed', t('Allow everybody to access to this project'), 1, $project['is_everybody_allowed']) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
<?php endif ?>

<div class="alert alert-info">
    <ul>
        <li><?= t('A project manager can change the settings of the project and have more privileges than a standard user.') ?></li>
        <li><?= t('Don\'t forget that administrators have access to everything.') ?></li>
        <li><?= $this->url->doc(t('Help with project permissions'), 'project-permissions') ?></li>
    </ul>
</div>
