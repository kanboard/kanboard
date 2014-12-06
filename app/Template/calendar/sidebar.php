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
    <!--<hr>
    <h2><?= t('iCal-Url') ?></h2>
    <input value="<?= $ical_url ?>" readonly><br>
    <i>uses Project-Token or Api token</i>-->
</li>
</ul>
</div>
