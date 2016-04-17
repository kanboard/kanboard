<header>
    <nav>
        <h1>
            <span class="logo">
                <?= $this->url->link('K<span>B</span>', 'app', 'index', array(), false, '', t('Dashboard')) ?>
            </span>
            <span class="title">
                <?= $this->text->e($title) ?>
            </span>
            <?php if (! empty($description)): ?>
                <span class="tooltip" title='<?= $this->text->e($this->text->markdown($description)) ?>'>
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
                        data-search-threshold="0"
                        data-notfound="<?= t('No results match:') ?>"
                        data-placeholder="<?= t('Display another project') ?>"
                        data-redirect-regex="PROJECT_ID"
                        data-redirect-url="<?= $this->url->href('board', 'show', array('project_id' => 'PROJECT_ID')) ?>">
                    <option value=""></option>
                    <?php foreach ($board_selector as $board_id => $board_name): ?>
                        <option value="<?= $board_id ?>"><?= $this->text->e($board_name) ?></option>
                    <?php endforeach ?>
                </select>
            </li>
            <?php endif ?>
            <li class="user-links">
                <?php if ($this->user->hasNotifications()): ?>
                    <span class="notification">
                        <?= $this->url->link('<i class="fa fa-bell web-notification-icon"></i>', 'app', 'notifications', array('user_id' => $this->user->getId()), false, '', t('Unread notifications')) ?>
                    </span>
                <?php endif ?>

                <?php $has_project_creation_access = $this->user->hasAccess('ProjectCreation', 'create'); ?>
                <?php $is_private_project_enabled = $this->app->config('disable_private_project', 0) == 0; ?>

                <?php if ($has_project_creation_access || (!$has_project_creation_access && $is_private_project_enabled)): ?>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-plus fa-fw"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <?php if ($has_project_creation_access): ?>
                            <li><?= $this->url->button('fa-plus', t('New project'), 'ProjectCreation', 'create', array(), false, 'popover') ?></li>
                        <?php endif ?>
                        <?php if ($is_private_project_enabled): ?>
                        <li>
                            <?= $this->url->button('fa-lock', t('New private project'), 'ProjectCreation', 'createPrivate', array(), false, 'popover') ?>
                        </li>
                        <?php endif ?>
                    </ul>
                </div>
                <?php endif ?>

                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><?= $this->avatar->currentUserSmall('avatar-inline') ?><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li class="no-hover"><strong><?= $this->text->e($this->user->getFullname()) ?></strong></li>
                        <li>
                            <?= $this->url->button('fa-tachometer', t('My dashboard'), 'app', 'index', array('user_id' => $this->user->getId())) ?>
                        </li>
                        <li>
                            <?= $this->url->button('fa-home', t('My profile'), 'user', 'show', array('user_id' => $this->user->getId())) ?>
                        </li>
                        <li>
                            <?= $this->url->button('fa-folder', t('Projects management'), 'project', 'index') ?>
                        </li>
                        <?php if ($this->user->hasAccess('user', 'index')): ?>
                            <li>
                                <?= $this->url->button('fa-user', t('Users management'), 'user', 'index') ?>
                            </li>
                            <li>
                                <?= $this->url->button('fa-group', t('Groups management'), 'group', 'index') ?>
                            </li>
                            <li>
                                <?= $this->url->button('fa-cog', t('Settings'), 'config', 'index') ?>
                            </li>
                        <?php endif ?>
                        <li>
                            <?= $this->url->button('fa-life-ring', t('Documentation'), 'doc', 'show') ?>
                        </li>
                        <?php if (! DISABLE_LOGOUT): ?>
                            <li>
                                <?= $this->url->button('fa-sign-out', t('Logout'), 'auth', 'logout') ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</header>
