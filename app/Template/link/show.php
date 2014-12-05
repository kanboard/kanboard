<?php if (! empty($links)): ?>
<li>
    <?= t('Links:') ?><ul>
    <?php foreach ($links as $link): ?>
        <li>
        <?php
        $linkName = $link['name'];
        $linkedTaskId = $link['task_inverse_id'];
        if ($link['task_inverse_id'] == $task['id']) {
            $linkName = $link['name_inverse'];
            $linkedTaskId = $link['task_id'];
        }
        ?>
        <?= Helper\escape($linkName) ?> <?= Helper\a('#'.Helper\escape($linkedTaskId), 'task', '', array('task_id' => $linkedTaskId, 'action' => 'show')) ?>
        </li>
    <?php endforeach ?>
    </ul>
</li>
<?php endif ?>
