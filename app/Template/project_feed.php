<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<feed xml:lang="en-US" xmlns="http://www.w3.org/2005/Atom">
    <title><?= t('%s\'s activity', $project['name']) ?></title>
    <link rel="alternate" type="text/html" href="<?= Helper\get_current_base_url() ?>"/>
    <link rel="self" type="application/atom+xml" href="<?= Helper\get_current_base_url().Helper\u('project', 'feed', array('token' => $project['token'])) ?>"/>
    <updated><?= date(DATE_ATOM) ?></updated>
    <id><?= Helper\get_current_base_url() ?></id>
    <icon><?= Helper\get_current_base_url() ?>assets/img/favicon.png</icon>

    <?php foreach ($events as $e): ?>
    <entry>
        <title type="text"><?= $e['event_title'] ?></title>
        <link rel="alternate" href="<?= Helper\get_current_base_url().Helper\u('task', 'show', array('task_id' => $e['task_id'])) ?>"/>
        <id><?= $e['id'].'-'.$e['event_name'].'-'.$e['task_id'].'-'.$e['date_creation'] ?></id>
        <published><?= date(DATE_ATOM, $e['date_creation']) ?></published>
        <updated><?= date(DATE_ATOM, $e['date_creation']) ?></updated>
        <author>
            <name><?= Helper\escape($e['author']) ?></name>
        </author>
        <content type="html">
            <![CDATA[
                <?= $e['event_content'] ?>
            ]]>
        </content>
    </entry>
    <?php endforeach ?>
</feed>