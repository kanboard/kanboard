<section id="main">
    <div class="page-header">
        <ul>
            <li><?= $this->url->icon('users', t('View all groups'), 'GroupListController', 'index') ?></li>
            <li><?= $this->modal->medium('plus', t('Add group member'), 'GroupListController', 'associate', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no user in this group.') ?></p>
    <?php else: ?>
        <table class="table-striped table-scrolling table-hover">
            <tr>
                <th class="column-5"><?= $paginator->order(t('Id'), 'id') ?></th>
                <th><?= $paginator->order(t('Username'), 'username') ?></th>
                <th><?= $paginator->order(t('Name'), 'name') ?></th>
                <th><?= $paginator->order(t('Email'), 'email') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $user): ?>
            <tr>
                <td>
                    <div class="dropdown">
                        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= '#'.$user['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
                        <ul>
                            <li>
                                <?= $this->url->icon('user', t('View profile'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
                            </li>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove this user from group'), 'GroupListController', 'dissociate', array('group_id' => $group['id'], 'user_id' => $user['id'])) ?>
                            </li>
                        </ul>
                    </div>
                </td>
                <td>
                    <?= $this->url->link($this->text->e($user['username']), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->text->e($user['name']) ?>
                </td>
                <td>
                    <a href="mailto:<?= $this->text->e($user['email']) ?>"><?= $this->text->e($user['email']) ?></a>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
