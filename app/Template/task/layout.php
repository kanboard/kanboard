<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->render('task/menu', array('task' => $task)) ?>
            </li>
        </ul>
        <ul class="btn-group">
            <li>
                <?= $this->url->buttonLink('<fa-th>' . t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id']), false, '', '', false, $task['swimlane_id'] != 0 ? 'swimlane-'.$task['swimlane_id'] : '') ?>
            </li>
            <li>
                <?= $this->url->buttonLink('<fa-calendar>' . t('Back to the calendar'), 'calendar', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $task['project_id'])): ?>
            <li>
                <?= $this->url->buttonLink('<fa-cog>' . t('Project settings'), 'project', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php endif ?>
        </ul>
    </div>
    <section class="sidebar-container">

        <?= $this->render($sidebar_template, array('task' => $task)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
