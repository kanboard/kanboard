<table class="table-striped table-small">
    <tr>
        <th class="column-20"><?= t('Type') ?></th>
        <th class="column-80"><?= t('Title') ?></th>
        <th class="column-10"><?= t('Dependency') ?></th>
    </tr>
    <?php foreach ($links as $link): ?>
        <tr>
            <td>
                <?= $link['type'] ?>
            </td>
            <td>
                <a href="<?= $link['url'] ?>" target="_blank"><?= $this->e($link['title']) ?></a>
            </td>
            <td>
                <?= $this->e($link['dependency_label']) ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>