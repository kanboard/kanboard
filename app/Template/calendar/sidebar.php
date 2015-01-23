<div class="sidebar">
    <ul class="no-bullet">
        <li>
            <?= t('Filter by user') ?>
        </li>
        <li>
            <?= $this->formSelect('owner_id', $users_list, array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by category') ?>
        </li>
        <li>
            <?= $this->formSelect('category_id', $categories_list, array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by column') ?>
        </li>
        <li>
            <?= $this->formSelect('column_id', $columns_list, array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by swimlane') ?>
        </li>
        <li>
            <?= $this->formSelect('swimlane_id', $swimlanes_list, array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by color') ?>
        </li>
        <li>
            <?= $this->formSelect('color_id', $colors_list, array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by status') ?>
        </li>
        <li>
            <?= $this->formSelect('is_active', $status_list, array(), array(), 'calendar-filter') ?>
        </li>
    </ul>
</div>
