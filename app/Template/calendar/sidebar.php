<div class="sidebar">
    <h2><?= t('Filter') ?></h2>
    <ul>
        <li>
            <?= t('Filter by project') ?><br>
            <?= $this->formSelect('project_id', $projects) ?>
        </li>
        <li>
            <?= t('Filter by user') ?><br>
            <?= $this->formSelect('user_id', $users) ?>
        </li>
        <li>
            <?= t('Filter by category') ?><br>
            <?= $this->formSelect('category_id', $categories) ?>
        </li>
        <li>
            <?= t('Filter by column') ?><br>
            <?= $this->formSelect('column_id', $columns) ?>
        </li>
        <li>
            <?= t('Filter by status') ?><br>
            <?= $this->formSelect('status_id', $status) ?>
        </li>
    </ul>
</li>
</ul>
</div>
