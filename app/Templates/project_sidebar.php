<div class="project-show-sidebar">
    <h2><?= t('Actions') ?></h2>
    <div class="project-show-actions">
        <ul>
            <li>
                <a href="?controller=project&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Summary') ?></a>
            </li>
            <li>
                <a href="?controller=project&amp;action=export&amp;project_id=<?= $project['id'] ?>"><?= t('Tasks Export') ?></a>
            </li>

            <?php if (Helper\is_admin()): ?>
            <li>
                <a href="?controller=project&amp;action=share&amp;project_id=<?= $project['id'] ?>"><?= t('Public access') ?></a>
            </li>
            <li>
                <a href="?controller=project&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit project') ?></a>
            </li>
            <li>
                <a href="?controller=board&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit board') ?></a>
            </li>
            <li>
                <a href="?controller=category&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('Categories management') ?></a>
            </li>
            <li>
                <a href="?controller=project&amp;action=users&amp;project_id=<?= $project['id'] ?>"><?= t('Users management') ?></a>
            </li>
            <li>
                <a href="?controller=action&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('Automatic actions') ?></a>
            </li>
            <li>
                <a href="?controller=project&amp;action=confirmDuplicate&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>"><?= t('Duplicate') ?></a>
            </li>
            <li>
                <?php if ($project['is_active']): ?>
                    <a href="?controller=project&amp;action=confirmDisable&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>"><?= t('Disable') ?></a>
                <?php else: ?>
                    <a href="?controller=project&amp;action=confirmEnable&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>"><?= t('Enable') ?></a>
                <?php endif ?>
            </li>
            <li>
                <a href="?controller=project&amp;action=confirmRemove&amp;project_id=<?= $project['id'] ?>"><?= t('Remove') ?></a>
            </li>
            <?php endif ?>
        </ul>
    </div>
</div>