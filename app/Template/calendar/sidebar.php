<div class="sidebar">
    <h2><?= t('Filter') ?></h2>
    <ul>
        <li>
            <?= t('Filter by project') ?><br>
            <?= Helper\form_select('project_id', $projects) ?>
        </li>
        <li>
            <?= t('Filter by user') ?><br>
            <?= Helper\form_select('user_id', $users) ?>
        </li>
        <li>
            <?= t('Filter by category') ?><br>
            <?= Helper\form_select('category_id', $categories) ?>
        </li>
        <li>
            <?= t('Filter by column') ?><br>
            <?= Helper\form_select('column_id', $columns) ?>
        </li>
        <li>
            <?= t('Filter by status') ?><br>
            <?= Helper\form_select('status_id', $status) ?>
        </li>
    </ul>
</li>
</ul>
</div>
