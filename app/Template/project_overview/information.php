<section class="accordion-section">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Information') ?></h3>
    </div>
    <div class="accordion-content">
        <div class="panel">
            <ul>
                <?php if ($project['owner_id'] > 0): ?>
                    <li><?= t('Project owner: ') ?><strong><?= $this->text->e($project['owner_name'] ?: $project['owner_username']) ?></strong></li>
                <?php endif ?>

                <?php if (! empty($users)): ?>
                    <?php foreach ($roles as $role => $role_name): ?>
                        <?php if (isset($users[$role])): ?>
                            <li>
                                <?= $this->text->e($role_name) ?>:
                                <strong><?= $this->text->implode(', ', $users[$role]) ?></strong>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>

                <?php if ($project['start_date']): ?>
                    <li><?= t('Start date: ').$this->dt->date($project['start_date']) ?></li>
                <?php endif ?>

                <?php if ($project['end_date']): ?>
                    <li><?= t('End date: ').$this->dt->date($project['end_date']) ?></li>
                <?php endif ?>

                <?php if ($project['is_public']): ?>
                    <li><?= $this->url->icon('share-alt', t('Public link'), 'BoardViewController', 'readonly', array('token' => $project['token']), false, '', '', true) ?></li>
                    <li><?= $this->url->icon('rss-square', t('RSS feed'), 'FeedController', 'project', array('token' => $project['token']), false, '', '', true) ?></li>
                    <li><?= $this->url->icon('calendar', t('iCal feed'), 'ICalendarController', 'project', array('token' => $project['token'])) ?></li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</section>
