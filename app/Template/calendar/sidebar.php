<div class="sidebar">
    <ul class="no-bullet">
        <li>
            <?= t('Filter by user') ?>
        </li>
        <li>
            <?= $this->form->select('owner_id', $users_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by category') ?>
        </li>
        <li>
            <?= $this->form->select('category_id', $categories_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by column') ?>
        </li>
        <li>
            <?= $this->form->select('column_id', $columns_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by swimlane') ?>
        </li>
        <li>
            <?= $this->form->select('swimlane_id', $swimlanes_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by color') ?>
        </li>
        <li>
            <?= $this->form->select('color_id', $colors_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
        <li>
            <?= t('Filter by status') ?>
        </li>
        <li>
            <?= $this->form->select('is_active', $status_list, array(), array(), array(), 'calendar-filter') ?>
        </li>
    </ul>
</div>
