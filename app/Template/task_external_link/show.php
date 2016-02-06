<div class="page-header">
    <h2><?= t('External links') ?></h2>
</div>

<?php if (empty($links)): ?>
    <p class="alert"><?= t('There is no external link for the moment.') ?></p>
<?php else: ?>
    <table class="table-stripped table-small">
        <tr>
            <th class="column-10"><?= t('Type') ?></th>
            <th><?= t('Title') ?></th>
            <th class="column-10"><?= t('Dependency') ?></th>
            <th class="column-15"><?= t('Creator') ?></th>
            <th class="column-15"><?= t('Date') ?></th>
            <?php if ($this->user->hasProjectAccess('TaskExternalLink', 'edit', $task['project_id'])): ?>
                <th class="column-5"><?= t('Action') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($links as $link): ?>
            <tr>
                <td>
                    <?= $link['type'] ?>
                </td>
                <td>
                    <a href="<?= $link['url'] ?>" target="_blank"><?= $this->e($link['title']) ?></a>
                </td>
                <td>
                    <?= $this->e($link['dependency_label']) ?>
                </td>
                <td>
                    <?= $this->e($link['creator_name'] ?: $link['creator_username']) ?>
                </td>
                <td>
                    <?= $this->dt->date($link['date_creation']) ?>
                </td>
                <?php if ($this->user->hasProjectAccess('TaskExternalLink', 'edit', $task['project_id'])): ?>
                <td>
                    <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li><?= $this->url->link(t('Edit'), 'TaskExternalLink', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
                        <li><?= $this->url->link(t('Remove'), 'TaskExternalLink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
                    </ul>
                    </div>
                </td>
                <?php endif ?>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
