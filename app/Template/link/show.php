<?php if (! empty($links)): ?>
<div id="links" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Liens') ?></h2>
    </div>
    <article>
        <ul>
        <?php foreach ($links as $link): ?>
            <li>
            <?= Helper\escape($link['name']) ?> <?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' '.Helper\escape($link['task_inverse_name']), 'task', '', array('task_id' => $link['task_inverse_id'], 'action' => 'show')) ?>
            
            <?= Helper\a(t('Edit'), 'tasklink', 'edit', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            <?= t('or') ?>
            <?= Helper\a(t('Remove'), 'tasklink', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            </li>
        <?php endforeach ?>
        </ul>
    </article>
</div>
<?php endif ?>
