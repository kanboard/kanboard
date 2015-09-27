<div class="page-header">
    <h2><?= t('Custom filters') ?></h2>
</div>
<div>
    <table>
        <tr>
            <th><?= t('Filter') ?></th>
            <th><?= t('Name') ?></th>
            <th><?= t('Shared') ?></th>
            <th><?= t('Created by') ?></th>
            <th><?= t('Actions') ?></th>
        </tr>
    <?php foreach ($custom_filters as $cf): ?>
         <tr>
            <td><?= $cf['filter'] ?></td>
            <td><?= $cf['name'] ?></td>
            <td><?= $cf['is_shared'] ?></td>
            <td><?= $this->e($cf['owner_name'] ?: $cf['owner_username']) ?></td>
            <td>
            <?php if ($cf['user_id'] == $user_id || $this->user->isAdmin()): ?>
                    <ul>
                        <li><?= $this->url->link(t('Remove'), 'customFilter', 'remove', array('project_id' => $cf['project_id'], 'user_id' => $cf['user_id'], 'filter' => $cf['filter']),true) ?></li>
                        <li><?= $this->url->link(t('Edit'), 'customFilter', 'edit', array('project_id' => $cf['project_id'], 'user_id' => $cf['user_id'], 'filter' => $cf['filter']),true) ?></li>
                    </ul>
            <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </table>
</div>


<div class="page-header">
    <h2><?= t('Add a new filter') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('customfilter', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?= $this->form->label(t('Filter'), 'filter') ?>
    <?= $this->form->text('filter', $values, $errors, array('autofocus', 'required', 'maxlength="80"')) ?>
    
    <?= $this->form->checkbox('is_shared', t('Share with other Members'), 1, 0) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>