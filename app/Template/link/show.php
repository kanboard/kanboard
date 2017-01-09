<div class="page-header">
    <h2><?= t('Link labels') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add link label'), 'LinkController', 'create') ?>
        </li>
    </ul>
</div>
<?php if (! empty($links)): ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th class="column-70"><?= t('Link labels') ?></th>
            <th><?= t('Actions') ?></th>
        </tr>
        <?php foreach ($links as $link): ?>
        <tr>
            <td>
                <strong><?= t($link['label']) ?></strong>

                <?php if (! empty($link['opposite_label'])): ?>
                    | <?= t($link['opposite_label']) ?>
                <?php endif ?>
            </td>
            <td>
                <ul>
                    <?= $this->modal->medium('edit', t('Edit'), 'LinkController', 'edit', array('link_id' => $link['id'])) ?>
                    <?= t('or') ?>
                    <?= $this->modal->confirm('trash-o', t('Remove'), 'LinkController', 'confirm', array('link_id' => $link['id'])) ?>
                </ul>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php else: ?>
    <?= t('There is no link.') ?>
<?php endif ?>
