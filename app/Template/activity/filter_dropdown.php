<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon" title="<?= t('Default filters') ?>"><i class="fa fa-filter fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li><a href="#" class="filter-helper filter-reset" data-filter="" title="<?= t('Keyboard shortcut: "%s"', 'r') ?>"><?= t('Reset filters') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="creator:me"><?= t('My activities') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="created:<=<?= date('Y-m-d', strtotime('yesterday')) ?>"><?= t('Activity until yesterday') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="created:<=<?= date('Y-m-d')?>"><?= t('Activity until today') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:closed"><?= t('Closed tasks') ?></a></li>
        <li><a href="#" class="filter-helper" data-filter="status:open"><?= t('Open tasks') ?></a></li>
        <li>
            <?= $this->url->doc(t('View advanced search syntax'), 'search') ?>
        </li>
    </ul>
</div>