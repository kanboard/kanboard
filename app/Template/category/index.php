<div class="page-header">
    <h2><?= t('Categories') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add a new category'), 'CategoryController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>
<?php if (empty($categories)): ?>
    <p class="alert"><?= t('There is no category in this project.') ?></p>
<?php else: ?>
    <table class="table-striped">
        <tr>
            <th><?= t('Category Name') ?></th>
            <th><?= t('Color') ?></th>
        </tr>
        <?php foreach ($categories as $category): ?>
        <tr>
            <td>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->medium('edit', t('Edit'), 'CategoryController', 'edit', array('project_id' => $project['id'], 'category_id' => $category['id'])) ?>
                        </li>
                        <li>
                            <?= $this->modal->confirm('trash-o', t('Remove'), 'CategoryController', 'confirm', array('project_id' => $project['id'], 'category_id' => $category['id'])) ?>
                        </li>
                    </ul>
                </div>

                <?= $this->text->e($category['name']) ?>

                <?php if (! empty($category['description'])): ?>
                    <?= $this->app->tooltipMarkdown($category['description']) ?>
                <?php endif ?>
            </td>
            <td><?= $this->text->e($colors[$category['color_id']] ?? '') ?></td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
