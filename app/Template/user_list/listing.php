<div class="page-header">
    <?php if ($this->user->hasAccess('UserCreationController', 'show')): ?>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('New user'), 'UserCreationController', 'show') ?>
        </li>
        <li>
            <?= $this->modal->medium('paper-plane', t('Invite people'), 'UserInviteController', 'show') ?>
        </li>
        <li>
            <?= $this->modal->medium('upload', t('Import'), 'UserImportController', 'show') ?>
        </li>
        <li>
            <?= $this->url->icon('users', t('View all groups'), 'GroupListController', 'index') ?>
        </li>
    </ul>
    <?php endif ?>
</div>

<div class="margin-bottom">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'UserListController')) ?>
        <?= $this->form->hidden('action', array('action' => 'search')) ?>
        <?= $this->form->text('search', $values, array(), array('placeholder="'.t('Search').'"', 'aria-label="'.t('Search').'"')) ?>
    </form>
</div>

<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No users found.') ?></p>
<?php elseif (! $paginator->isEmpty()): ?>
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
