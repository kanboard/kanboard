<div class="page-header">
    <h2><?= t('Project tags') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add new tag'), 'ProjectTagController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>

<?php if (empty($tags)): ?>
    <p class="alert"><?= t('There is no specific tag for this project at the moment.') ?></p>
<?php else: ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th><?= t('Tag') ?></th>
        </tr>
        <?php foreach ($tags as $tag): ?>
            <tr>
                <td>
                    <div class="dropdown">
                        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                        <ul>
                            <li>
                                <?= $this->modal->medium('edit', t('Edit'), 'ProjectTagController', 'edit', array('tag_id' => $tag['id'], 'project_id' => $project['id'])) ?>
                            </li>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove'), 'ProjectTagController', 'confirm', array('tag_id' => $tag['id'], 'project_id' => $project['id'])) ?>
                            </li>
                        </ul>
                    </div>
                    <?= $this->text->e($tag['name']) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
