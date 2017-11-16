<div class="page-header">
    <ul>
        <li><?= $this->url->icon('user', t('All users'), 'UserListController', 'show') ?></li>
        <li><?= $this->modal->medium('user-plus', t('New group'), 'GroupCreationController', 'show') ?></li>
    </ul>
</div>

<div class="margin-bottom">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'GroupListController')) ?>
        <?= $this->form->hidden('action', array('action' => 'index')) ?>
        <?= $this->form->text('search', $values, array(), array('placeholder="'.t('Search').'"')) ?>
    </form>
</div>

<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is no group.') ?></p>
<?php else: ?>
    <div class="table-list">
        <div class="table-list-header">
            <div class="table-list-header-count">
                <?php if ($paginator->getTotal() > 1): ?>
                    <?= t('%d groups', $paginator->getTotal()) ?>
                <?php else: ?>
                    <?= t('%d group', $paginator->getTotal()) ?>
                <?php endif ?>
            </div>
            <div class="table-list-header-menu">
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
                    <ul>
                        <li>
                            <?= $paginator->order(t('Group ID'), \Kanboard\Model\GroupModel::TABLE.'.id') ?>
                        </li>
                        <li>
                            <?= $paginator->order(t('Name'), \Kanboard\Model\GroupModel::TABLE.'.name') ?>
                        </li>
                        <li>
                            <?= $paginator->order(t('External ID'), \Kanboard\Model\GroupModel::TABLE.'.external_id') ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <?php foreach ($paginator->getCollection() as $group): ?>
        <div class="table-list-row table-border-left">
            <span class="table-list-title">
                <?= $this->render('group/dropdown', array('group' => $group)) ?>
                <?= $this->url->link($this->text->e($group['name']), 'GroupListController', 'users', array('group_id' => $group['id'])) ?>
            </span>

            <div class="table-list-details">
                <ul>
                    <?php if ($group['nb_users'] > 1): ?>
                        <li><?= t('%d users', $group['nb_users']) ?></li>
                    <?php else: ?>
                        <li><?= t('%d user', $group['nb_users']) ?></li>
                    <?php endif ?>

                    <?php if (! empty($group['external_id'])): ?>
                        <li><?= $this->text->e($group['external_id']) ?></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <?= $paginator ?>
<?php endif ?>
