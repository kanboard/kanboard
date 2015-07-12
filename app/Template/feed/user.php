<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<feed xml:lang="en-US" xmlns="http://www.w3.org/2005/Atom">
    <title><?= t('Project activities for %s', $user['name'] ?: $user['username']) ?></title>
    <link rel="alternate" type="text/html" href="<?= $this->url->base() ?>"/>
    <link rel="self" type="application/atom+xml" href="<?= $this->url->href('feed', 'user', array('token' => $user['token']), false, '', true) ?>"/>
    <updated><?= date(DATE_ATOM) ?></updated>
    <id><?= $this->url->href('feed', 'user', array('token' => $user['token']), false, '', true) ?></id>
    <icon><?= $this->url->base() ?>assets/img/favicon.png</icon>

    <?php foreach ($events as $e): ?>
    <entry>
        <title type="text"><?= $e['event_title'] ?></title>
        <link rel="alternate" href="<?= $this->url->href('task', 'show', array('task_id' => $e['task_id']), false, '', true) ?>"/>
        <id><?= $e['id'].'-'.$e['event_name'].'-'.$e['task_id'].'-'.$e['date_creation'] ?></id>
        <published><?= date(DATE_ATOM, $e['date_creation']) ?></published>
        <updated><?= date(DATE_ATOM, $e['date_creation']) ?></updated>
        <author>
            <name><?= $this->e($e['author']) ?></name>
        </author>
        <content type="html">
            <![CDATA[
                <?= $e['event_content'] ?>
            ]]>
        </content>
    </entry>
    <?php endforeach ?>
</feed>