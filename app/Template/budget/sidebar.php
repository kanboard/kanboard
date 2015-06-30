<div class="sidebar">
    <h2><?= t('Budget') ?></h2>
    <ul>
        <li>
            <?= $this->url->link(t('Budget overview'), 'budget', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Budget lines'), 'budget', 'create', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->url->link(t('Cost breakdown'), 'budget', 'breakdown', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>