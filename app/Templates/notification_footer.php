<hr/>
Kanboard

<?php if (defined('KANBOARD_URL')): ?>
    - <a href="<?= KANBOARD_URL.'?controller=task&action=show&task_id='.$task['id'] ?>"><?= t('view the task on Kanboard') ?></a>.
<?php endif ?>
