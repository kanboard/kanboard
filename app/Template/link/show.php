<?php if (! empty($links)): ?>
<aside id="links" class="task-show-section">
        <ul>
        <?php foreach ($links as $link): ?>
            <li>
            <?= Helper\escape($link['name']) ?>
            <?php if (0 == $link['task_inverse_is_active']): ?><span class="task-closed"><?php endif ?><?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' '.trim(Helper\escape($link['task_inverse_name'])), 'task', '', array('task_id' => $link['task_inverse_id'], 'action' => 'show')) ?><?php if (0 == $link['task_inverse_is_active']): ?></span><?php endif ?>
            
            <?= Helper\a(t('Edit'), 'tasklink', 'edit', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            <?= t('or') ?>
            <?= Helper\a(t('Remove'), 'tasklink', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            </li>
        <?php endforeach ?>
        </ul>
</aside>
<?php endif ?>
