<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->userSession->isAdmin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= $this->a(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= $this->a(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
        </ul>
    </div>
    <section>
        <?php if ($paginator->isEmpty()): ?>
            <p class="alert"><?= t('No project') ?></p>
        <?php else: ?>
            <table class="table-fixed">
                <tr>
                    <th class="column-8"><?= $paginator->order(t('Id'), 'id') ?></th>
                    <th class="column-8"><?= $paginator->order(t('Status'), 'is_active') ?></th>
                    <th class="column-8"><?= $paginator->order(t('Identifier'), 'identifier') ?></th>
                    <th class="column-20"><?= $paginator->order(t('Project'), 'name') ?></th>
                    <th><?= t('Columns') ?></th>
                </tr>
                <?php foreach ($paginator->getCollection() as $project): ?>
                <tr>
                    <td>
                        <?= $this->a('#'.$project['id'], 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link') ?>
                    </td>
                    <td>
                        <?php if ($project['is_active']): ?>
                            <?= t('Active') ?>
                        <?php else: ?>
                            <?= t('Inactive') ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?= $this->e($project['identifier']) ?>
                    </td>
                    <td>
                        <?= $this->a('<i class="fa fa-table"></i>', 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Board')) ?>&nbsp;

                        <?php if ($project['is_public']): ?>
                            <i class="fa fa-share-alt fa-fw"></i>
                        <?php endif ?>
                        <?php if ($project['is_private']): ?>
                            <i class="fa fa-lock fa-fw"></i>
                        <?php endif ?>

                        <?= $this->a($this->e($project['name']), 'project', 'show', array('project_id' => $project['id'])) ?>
                        <?php if (! empty($project['description'])): ?>
                            <span class="column-tooltip" title='<?= $this->e($this->markdown($project['description'])) ?>'>
                                <i class="fa fa-info-circle"></i>
                            </span>
                        <?php endif ?>
                    </td>
                    <td class="dashboard-project-stats">
                        <?php foreach ($project['columns'] as $column): ?>
                            <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
                            <span><?= $this->e($column['title']) ?></span>
                        <?php endforeach ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>

            <?= $paginator ?>
        <?php endif ?>
    </section>
</section>
