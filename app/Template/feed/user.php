<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?= t('Project activities for %s', $this->user->getFullname($user)) ?></title>
    <updated><?= date(DATE_ATOM) ?></updated>
    <link rel="alternate" type="text/html" href="<?= $this->url->base() ?>"/>
    <link rel="self" type="application/atom+xml" href="<?= $this->url->href('FeedController', 'user', ['token' => $user['token']], false, '', true) ?>"/>
    <id><?= $this->url->href('FeedController', 'user', ['token' => $user['token']], false, '', true) ?></id>

    <?php foreach ($events as $event): ?>
        <entry>
            <id><?= $this->url->href('TaskViewController', 'show', ['task_id' => $event['task_id']], false, 'event-'.$event['id'], true) ?></id>
            <link rel="alternate" type="text/html" href="<?= $this->url->href('TaskViewController', 'show', ['task_id' => $event['task_id']], false, '', true) ?>"/>
            <updated><?= date(DATE_ATOM, $event['date_creation']) ?></updated>
            <published><?= date(DATE_ATOM, $event['date_creation']) ?></published>
            <author>
                <name><?= htmlentities($event['author'], ENT_XML1) ?></name>
            </author>
            <title><?= htmlentities($event['event_title'], ENT_XML1) ?></title>
            <content type="html"><![CDATA[<?= $event['event_content'] ?>]]></content>
        </entry>
    <?php endforeach ?>

</feed>