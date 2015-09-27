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
            <td>
            <?php if ($cf['is_shared'] == 1): ?>
                <?= t('yes') ?>
            <?php else: ?>
                <?= t('no') ?>
            <?php endif ?>
            </td>
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

<?= $this->render('customfilter/add', array('project' => $project, 'values' => $values, 'errors' => $errors)) ?>