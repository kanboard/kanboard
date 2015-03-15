<div class="page-header">
    <h2><?= t('Budget') ?></h2>
    <ul>
        <li><?= $this->a(t('Budget lines'), 'budget', 'create', array('project_id' => $project['id'])) ?></li>
        <li><?= $this->a(t('Cost breakdown'), 'budget', 'breakdown', array('project_id' => $project['id'])) ?></li>
    </ul>
</div>

<p><?= t('Current budget: ') ?><strong><?= n($total) ?></strong></p>
