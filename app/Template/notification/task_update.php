<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<?= t('The task have been updated.') ?>

<?php if (! empty($changes)): ?>
    <h2><?= t('List of changes') ?></h2>
    <ul>
        <?php

        foreach ($changes as $field => $value) {

            switch ($field) {
                case 'title':
                    echo '<li>'.t('New title: %s', $task['title']).'</li>';
                    break;
                case 'owner_id':
                    if (empty($task['owner_id'])) {
                        echo '<li>'.t('The task is not assigned anymore').'</li>';
                    }
                    else {
                        echo '<li>'.t('New assignee: %s', $task['assignee_name'] ?: $task['assignee_username']).'</li>';
                    }
                    break;
                case 'category_id':
                    if (empty($task['category_id'])) {
                        echo '<li>'.t('There is no category now').'</li>';
                    }
                    else {
                        echo '<li>'.t('New category: %s', $task['category_name']).'</li>';
                    }
                    break;
                case 'color_id':
                    echo '<li>'.t('New color: %s', $this->text->in($task['color_id'], $colors_list)).'</li>';
                    break;
                case 'score':
                    echo '<li>'.t('New complexity: %d', $task['score']).'</li>';
                    break;
                case 'date_due':
                    if (empty($task['date_due'])) {
                        echo '<li>'.t('The due date have been removed').'</li>';
                    }
                    else {
                        echo '<li>'.dt('New due date: %B %e, %Y', $task['date_due']).'</li>';
                    }
                    break;
                case 'description':
                    if (empty($task['description'])) {
                        echo '<li>'.t('There is no description anymore').'</li>';
                    }
                    break;
                default:
                    echo '<li>'.t('The field "%s" have been updated', $field).'</li>';
            }
        }

        ?>
    </ul>

    <?php if (! empty($changes['description'])): ?>
        <h3><?= t('New description') ?></h3>
        <?= $this->text->markdown($task['description']) ?>
    <?php endif ?>
<?php endif ?>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>