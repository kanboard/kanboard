<section id="main">
    <div class="page-header">
        <h2><?= Helper\escape($task['project_name']) ?> &gt; <?= t('Task #%d', $task['id']) ?></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('Back to the board') ?></a></li>
        </ul>
    </div>
    <section class="task-show">

        <div class="task-show-sidebar">

            <h2><?= t('Actions') ?></h2>
            <div class="task-show-actions">
                <ul>
                    <li><a href="?controller=task&amp;action=duplicate&amp;project_id=<?= $task['project_id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('Duplicate') ?></a></li>
                    <li><a href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>"><?= t('Edit') ?></a></li>
                    <li>
                        <?php if ($task['is_active'] == 1): ?>
                            <a href="?controller=task&amp;action=confirmClose&amp;task_id=<?= $task['id'] ?>"><?= t('Close this task') ?></a>
                        <?php else: ?>
                            <a href="?controller=task&amp;action=confirmOpen&amp;task_id=<?= $task['id'] ?>"><?= t('Open this task') ?></a>
                        <?php endif ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="task-show-main">
            <article class="task task-<?= $task['color_id'] ?> task-show-details">
                <h2><?= Helper\escape($task['title']) ?></h2>
            <?php if ($task['score']): ?>
                <span class="task-score"><?= Helper\escape($task['score']) ?></span>
            <?php endif ?>
            <ul>
                <li>
                    <?= dt('Created on %B %e, %G at %k:%M %p', $task['date_creation']) ?>
                </li>
                <?php if ($task['date_completed']): ?>
                <li>
                    <?= dt('Completed on %B %e, %G at %k:%M %p', $task['date_completed']) ?>
                </li>
                <?php endif ?>
                <?php if ($task['date_due']): ?>
                <li>
                    <strong><?= dt('Must be done before %B %e, %G', $task['date_due']) ?></strong>
                </li>
                <?php endif ?>
                <li>
                    <strong>
                    <?php if ($task['username']): ?>
                        <?= t('Assigned to %s', $task['username']) ?>
                    <?php else: ?>
                        <?= t('There is nobody assigned') ?>
                    <?php endif ?>
                    </strong>
                </li>
                <li>
                    <?= t('Column on the board:') ?>
                    <strong><?= Helper\escape($task['column_title']) ?></strong>
                    (<?= Helper\escape($task['project_name']) ?>)
                </li>
                <li>
                    <?php if ($task['is_active'] == 1): ?>
                        <?= t('Status is open') ?>
                    <?php else: ?>
                        <?= t('Status is closed') ?>
                    <?php endif ?>
                </li>
            </ul>
            </article>

            <h2><?= t('Description') ?></h2>
            <?php if ($task['description']): ?>
                <article class="markdown task-show-description">
                    <?= Helper\parse($task['description']) ?: t('There is no description.') ?>
                </article>
            <?php else: ?>
                <form method="post" action="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>" autocomplete="off">

                    <?= Helper\form_hidden('id', $description_form['values']) ?>
                    <?= Helper\form_textarea('description', $description_form['values'], $description_form['errors'], array('required', 'placeholder="'.t('Leave a description').'"')) ?><br/>
                    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

                    <div class="form-actions">
                        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                    </div>
                </form>
            <?php endif ?>

            <h2><?= t('Comments') ?></h2>
            <?php if ($comments): ?>
                <ul id="comments">
                <?php foreach ($comments as $comment): ?>
                    <?= Helper\template('comment_show', array(
                        'comment' => $comment,
                        'task' => $task,
                        'display_edit_form' => $comment['id'] == $comment_edit_form['values']['id'],
                        'values' => $comment_edit_form['values'] + array('comment' => $comment['comment']),
                        'errors' => $comment_edit_form['errors']
                    )) ?>
                <?php endforeach ?>
                </ul>
            <?php endif ?>

            <?php if (! isset($hide_comment_form) || $hide_comment_form === false): ?>
            <form method="post" action="?controller=comment&amp;action=save&amp;task_id=<?= $task['id'] ?>" autocomplete="off">

                <?= Helper\form_hidden('task_id', $comment_form['values']) ?>
                <?= Helper\form_hidden('user_id', $comment_form['values']) ?>
                <?= Helper\form_textarea('comment', $comment_form['values'], $comment_form['errors'], array('required', 'placeholder="'.t('Leave a comment').'"'), 'comment-textarea') ?><br/>
                <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

                <div class="form-actions">
                    <input type="submit" value="<?= t('Post comment') ?>" class="btn btn-blue"/>
                </div>
            </form>
            <?php endif ?>
        </div>

    </section>
</section>
