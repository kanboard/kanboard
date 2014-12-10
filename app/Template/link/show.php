<?php if (! empty($links)): ?>
<div id="links" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Liens') ?></h2>
    </div>
    <article>
        <ul>
        <?php foreach ($links as $link): ?>
            <li>
            <?php
            $linkName = $link['name'];
            $linkedTaskId = $link['task_inverse_id'];
            $linkedTaskName = $link['task_inverse_name'];
            if ($link['task_inverse_id'] == $task['id']) {
                $linkName = $link['name_inverse'];
                $linkedTaskId = $link['task_id'];
                $linkedTaskName = $link['task_name'];
            }
            ?>
            <?= Helper\escape($linkName) ?> <?= Helper\a('#'.Helper\escape($linkedTaskId).' '.Helper\escape($linkedTaskName), 'task', '', array('task_id' => $linkedTaskId, 'action' => 'show')) ?>
            <?= Helper\a(t('Edit'), 'link', 'create', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            <?= t('or') ?>
            <?= Helper\a(t('Remove'), 'link', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            </li>
        <?php endforeach ?>
        </ul>
    </article>
</div>
<?php endif ?>
