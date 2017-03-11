<section id="main">
    <div class="page-header">
        <ul>
            <li><?= $this->url->icon('user', t('All users'), 'UserListController', 'show') ?></li>
            <li><?= $this->url->icon('users', t('View all groups'), 'GroupListController', 'index') ?></li>
            <li><?= $this->modal->medium('plus', t('Add group member'), 'GroupListController', 'associate', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no user in this group.') ?></p>
    <?php else: ?>
        <div class="table-list">
            <?= $this->render('user_list/header', array('paginator' => $paginator)) ?>
            <?php foreach ($paginator->getCollection() as $user): ?>
                <div class="table-list-row table-border-left">
                    <?= $this->render('user_list/user_title', array(
                        'user' => $user,
                    )) ?>

                    <?= $this->render('user_list/user_details', array(
                        'user' => $user,
                    )) ?>

                    <?= $this->render('user_list/user_icons', array(
                        'user' => $user,
                    )) ?>
                </div>
            <?php endforeach ?>
        </div>

        <?= $paginator ?>
    <?php endif ?>
</section>
