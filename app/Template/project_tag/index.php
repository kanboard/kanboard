<div class="page-header">
    <h2><?= t('Project tags') ?></h2>
    <ul>
        <li>
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?= $this->url->link(t('Add new tag'), 'ProjectTagController', 'create', array('project_id' => $project['id']), false, 'popover') ?>
        </li>
    </ul>
</div>

<?php if (empty($tags)): ?>
    <p class="alert"><?= t('There is no specific tag for this project at the moment.') ?></p>
<?php else: ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th class="column-80"><?= t('Tag') ?></th>
            <th><?= t('Action') ?></th>
        </tr>
        <?php foreach ($tags as $tag): ?>
            <tr>
                <td><?= $this->text->e($tag['name']) ?></td>
                <td>
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <?= $this->url->link(t('Remove'), 'ProjectTagController', 'confirm', array('tag_id' => $tag['id'], 'project_id' => $project['id']), false, 'popover') ?>
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    <?= $this->url->link(t('Edit'), 'ProjectTagController', 'edit', array('tag_id' => $tag['id'], 'project_id' => $project['id']), false, 'popover') ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
