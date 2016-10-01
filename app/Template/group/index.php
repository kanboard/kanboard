<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'UserListController', 'show') ?></li>
            <li><i class="fa fa-user-plus fa-fw"></i><?= $this->url->link(t('New group'), 'GroupCreationController', 'show', array(), false, 'popover') ?></li>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no group.') ?></p>
    <?php else: ?>
        <table class="table-small table-fixed table-scrolling">
            <tr>
                <th class="column-5"><?= $paginator->order(t('Id'), 'id') ?></th>
                <th class="column-20"><?= $paginator->order(t('External Id'), 'external_id') ?></th>
                <th><?= $paginator->order(t('Name'), 'name') ?></th>
                <th class="column-5"><?= t('Actions') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $group): ?>
            <tr>
                <td>
                    #<?= $group['id'] ?>
                </td>
                <td>
                    <?= $this->text->e($group['external_id']) ?>
                </td>
                <td>
                    <?= $this->url->link($this->text->e($group['name']), 'GroupListController', 'users', array('group_id' => $group['id'])) ?>
                </td>
                <td>
                    <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li><?= $this->url->link(t('Add group member'), 'GroupListController', 'associate', array('group_id' => $group['id']), false, 'popover') ?></li>
                        <li><?= $this->url->link(t('Members'), 'GroupListController', 'users', array('group_id' => $group['id'])) ?></li>
                        <li><?= $this->url->link(t('Edit'), 'GroupModificationController', 'show', array('group_id' => $group['id']), false, 'popover') ?></li>
                        <li><?= $this->url->link(t('Remove'), 'GroupListController', 'confirm', array('group_id' => $group['id']), false, 'popover') ?></li>
                    </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
