<div>
    <?php if ($this->user->hasProjectAccess('ProjectViewController', 'show', $project['id'])): ?>
        <?= $this->render('project/dropdown', array('project' => $project)) ?>
    <?php else: ?>
        <strong><?= '#'.$project['id'] ?></strong>
    <?php endif ?>

    <span class="table-list-title <?= $project['is_active'] == 0 ? 'status-closed' : '' ?>">
        <?= $this->url->link($this->text->e($project['name']), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
    </span>
</div>
