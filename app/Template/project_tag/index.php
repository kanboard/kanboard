<div class="page-header">
    <h2><?= t('Project tags') ?></h2>
    <ul class="no-bullet">
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
            <th><?= t('Color') ?></th>
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
                            <?php if ($this->user->isAdmin()): ?>
                            <li>
                                <?= $this->modal->confirm('globe', t('Change to global tag'), 'ProjectTagController', 'confirmMakeGlobalTag', array('tag_id' => $tag['id'], 'project_id' => $project['id'])) ?>
                            </li>
                            <?php endif ?>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove'), 'ProjectTagController', 'confirm', array('tag_id' => $tag['id'], 'project_id' => $project['id'])) ?>
                            </li>
                        </ul>
                    </div>
                    <?= $this->text->e($tag['name']) ?>
                </td>
                <td><?= $this->text->e($colors[$tag['color_id']] ?? '') ?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Global tags') ?></h2>
</div>

<div class="panel">
    <form method="post" action="<?= $this->url->href('ProjectTagController', 'updateSettings', array('project_id' => $project['id'])) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>
        
        <?= $this->form->checkbox('enable_global_tags', t('Enable global tags for this project'), 1, $project['enable_global_tags'] == 1) ?>

        <?= $this->modal->submitButtons() ?>
    </form>
</div>
