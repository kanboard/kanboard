<?php if (! empty($custom_filters)): ?>
<div class="page-header">
    <h2><?= t('Custom filters') ?></h2>
</div>
<div>
    <table>
        <tr>
            <th><?= t('Name') ?></th>
            <th><?= t('Filter') ?></th>
            <th><?= t('Shared') ?></th>
            <th><?= t('Append/Replace') ?></th>
            <th><?= t('Owner') ?></th>
            <th><?= t('Actions') ?></th>
        </tr>
    <?php foreach ($custom_filters as $filter): ?>
         <tr>
            <td><?= $this->e($filter['name']) ?></td>
            <td><?= $this->e($filter['filter']) ?></td>
            <td>
            <?php if ($filter['is_shared'] == 1): ?>
                <?= t('Yes') ?>
            <?php else: ?>
                <?= t('No') ?>
            <?php endif ?>
            </td>
            <td>
            <?php if ($filter['append'] == 1): ?>
                <?= t('Append') ?>
            <?php else: ?>
                <?= t('Replace') ?>
            <?php endif ?>
            </td>
            <td><?= $this->e($filter['owner_name'] ?: $filter['owner_username']) ?></td>
            <td>
                <?php if ($filter['user_id'] == $this->user->getId() || $this->user->isProjectManagementAllowed($project['id'])): ?>
                    <ul>
                        <li><?= $this->url->link(t('Remove'), 'customfilter', 'remove', array('project_id' => $filter['project_id'], 'filter_id' => $filter['id']), true) ?></li>
                        <li><?= $this->url->link(t('Edit'), 'customfilter', 'edit', array('project_id' => $filter['project_id'], 'filter_id' => $filter['id'])) ?></li>
                    </ul>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </table>
</div>
<?php endif ?>

<?= $this->render('custom_filter/add', array('project' => $project, 'values' => $values, 'errors' => $errors)) ?>
