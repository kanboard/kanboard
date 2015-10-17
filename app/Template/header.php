<header>
    <nav>
        <h1><?= $this->url->link('K<span>B</span>', 'app', 'index', array(), false, 'logo', t('Dashboard')).' '.$this->e($title) ?>
            <?php if (! empty($description)): ?>
                <span class="tooltip" title='<?= $this->e($this->text->markdown($description)) ?>'>
                    <i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>
        </h1>
        <ul>
            <?php if (isset($board_selector) && ! empty($board_selector)): ?>
            <li>
                <select id="board-selector"
                        class="chosen-select select-auto-redirect"
                        tabindex="-1"
                        data-notfound="<?= t('No results match:') ?>"
                        data-placeholder="<?= t('Display another project') ?>"
                        data-redirect-regex="PROJECT_ID"
                        data-redirect-url="<?= $this->url->href('board', 'show', array('project_id' => 'PROJECT_ID')) ?>">
                    <option value=""></option>
                    <?php foreach ($board_selector as $board_id => $board_name): ?>
                        <option value="<?= $board_id ?>"><?= $this->e($board_name) ?></option>
                    <?php endforeach ?>
                </select>
            </li>
            <?php endif ?>
            <li>
                <?php if ($this->user->hasNotifications()): ?>
                    <?= $this->url->link('<i class="fa fa-bell web-notification-icon"></i>', 'app', 'notifications', array('user_id' => $this->user->getId()), false, '', t('Unread notifications')) ?>
                <?php endif ?>

                <?= $this->url->link(t('Logout'), 'auth', 'logout') ?>
                <span class="username hide-tablet">(<?= $this->user->getProfileLink() ?>)</span>
            </li>
        </ul>
    </nav>
</header>