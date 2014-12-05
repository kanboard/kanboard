<div class="sidebar">
    <h2><?= t('Filter') ?></h2>
    <ul>
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
        </li>
        <li>
            <?= t('Filter by status') ?><br>
        </li>
        </ul>
        <hr>
    
            <h2><?= t('iCal-Url') ?></h2>
            <input value="url" readonly><br>
            <i>uses Project-Token or Api token</i>
        </li>
    </ul>
</div>
