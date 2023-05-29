<div class="tooltip-large">
    <table class="table-small">
        <tr>
            <th class="column-20"><?= t('Type') ?></th>
            <th class="column-70"><?= t('Title') ?></th>
            <th class="column-10"><?= t('Dependency') ?></th>
        </tr>
        <?php foreach ($links as $link): ?>
            <tr>
                <td>
                    <?= $link['type'] ?>
                </td>
                <td>
                    <a href="<?= $this->text->e($link['url']) ?>" title="<?= $this->text->e($link['url']) ?>" target="_blank"><?= $this->text->e($link['title']) ?></a>
                </td>
                <td>
                    <?= $this->text->e($link['dependency_label']) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
