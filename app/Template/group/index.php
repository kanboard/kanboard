<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-user-plus fa-fw"></i><?= $this->url->link(t('New group'), 'group', 'create') ?></li>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no group.') ?></p>
    <?php else: ?>
        <table class="table-small">
            <tr>
                <th class="column-5"><?= $paginator->order(t('Id'), 'id') ?></th>
                <th class="column-20"><?= $paginator->order(t('External Id'), 'external_id') ?></th>
                <th><?= $paginator->order(t('Name'), 'name') ?></th>
                <th class="column-20"><?= t('Actions') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $group): ?>
            <tr>
                <td>
                    #<?= $group['id'] ?>
                </td>
                <td>
                    <?= $this->e($group['external_id']) ?>
                </td>
                <td>
                    <?= $this->e($group['name']) ?>
                </td>
                <td>
                    <ul>
                        <li><?= $this->url->link(t('Add group member'), 'group', 'associate', array('group_id' => $group['id'])) ?></li>
                        <li><?= $this->url->link(t('Members'), 'group', 'users', array('group_id' => $group['id'])) ?></li>
                        <li><?= $this->url->link(t('Edit'), 'group', 'edit', array('group_id' => $group['id'])) ?></li>
                        <li><?= $this->url->link(t('Remove'), 'group', 'confirm', array('group_id' => $group['id'])) ?></li>
                    </ul>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
