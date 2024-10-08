<?php if (! empty($links)): ?>
<table class="table-striped table-scrolling">
    <thead>
        <tr>
            <th class="column-15"><?= t('Type') ?></th>
            <th><?= t('Title') ?></th>
            <th class="column-15"><?= t('Dependency') ?></th>
            <th class="column-15"><?= t('Creator') ?></th>
            <th class="column-10"><?= t('Date') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($links as $link): ?>
        <tr>
            <td>
                <?php if ($this->user->hasProjectAccess('TaskExternalLinkController', 'edit', $task['project_id'])): ?>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->medium('edit', t('Edit'), 'TaskExternalLinkController', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->confirm('trash-o', t('Remove'), 'TaskExternalLinkController', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'])) ?>
                        </li>
                    </ul>
                </div>
                <?php endif ?>
                <?= $this->text->e($link['type']) ?>
            </td>
            <td>
                <a href="<?= $this->text->e($link['url']) ?>" title="<?= $this->text->e($link['url']) ?>" target="_blank"><?= $this->text->e($link['title']) ?><span class="ui-helper-hidden-accessible"> (<?= $this->text->e($link['url']) ?>)</span></a>
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
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php endif ?>
