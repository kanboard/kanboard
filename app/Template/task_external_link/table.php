<?php if (! empty($links)): ?>
<table class="table-striped table-scrolling">
    <tr>
        <th class="column-10"><?= t('Type') ?></th>
        <th><?= t('Title') ?></th>
        <th class="column-10"><?= t('Dependency') ?></th>
        <th class="column-15"><?= t('Creator') ?></th>
        <th class="column-15"><?= t('Date') ?></th>
        <?php if ($this->user->hasProjectAccess('TaskExternalLinkController', 'edit', $task['project_id'])): ?>
            <th class="column-5"><?= t('Action') ?></th>
        <?php endif ?>
    </tr>
    <?php foreach ($links as $link): ?>
        <tr>
            <td>
                <?= $link['type'] ?>
            </td>
            <td>
                <a href="<?= $link['url'] ?>" target="_blank"><?= $this->text->e($link['title']) ?></a>
            </td>
            <td>
                <?= $this->text->e($link['dependency_label']) ?>
            </td>
            <td>
                <?= $this->text->e($link['creator_name'] ?: $link['creator_username']) ?>
            </td>
            <td>
                <?= $this->dt->date($link['date_creation']) ?>
            </td>
            <?php if ($this->user->hasProjectAccess('TaskExternalLinkController', 'edit', $task['project_id'])): ?>
                <td>
                    <div class="dropdown">
                        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                        <ul>
                            <li>
                                <i class="fa fa-edit fa-fw"></i>
                                <?= $this->url->link(t('Edit'), 'TaskExternalLinkController', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
                            </li>
                            <li>
                                <i class="fa fa-trash-o fa-fw"></i>
                                <?= $this->url->link(t('Remove'), 'TaskExternalLinkController', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
                            </li>
                        </ul>
                    </div>
                </td>
            <?php endif ?>
        </tr>
    <?php endforeach ?>
</table>
<?php endif ?>
