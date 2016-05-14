<div class="dropdown">
    <a href="#" class="dropdown-menu dashboard-table-link">#<?= $project['id'] ?></a>
    <ul>
        <li>
            <i class="fa fa-th fa-fw"></i>
            <?= $this->url->link(t('Board'), 'board', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-calendar fa-fw"></i>
            <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <i class="fa fa-list fa-fw"></i>
            <?= $this->url->link(t('Listing'), 'listing', 'show', array('project_id' => $project['id'])) ?>
        </li>
        <?php if ($this->user->hasProjectAccess('Gantt', 'project', $project['id'])): ?>
        <li>
            <i class="fa fa-sliders fa-fw"></i>
            <?= $this->url->link(t('Gantt'), 'gantt', 'project', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>

        <li>
            <i class="fa fa-dashboard fa-fw"></i>&nbsp;
            <?= $this->url->link(t('Activity'), 'activity', 'project', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->hasProjectAccess('analytic', 'tasks', $project['id'])): ?>
            <li>
                <i class="fa fa-line-chart fa-fw"></i>&nbsp;
                <?= $this->url->link(t('Analytics'), 'analytic', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:project:dropdown', array('project' => $project)) ?>

        <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
