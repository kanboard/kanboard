<div class="page-header">
    <h2><?= t('Custom colors') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->url->href('config', 'color') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <table class="color-settings">
    <thead>
        <tr>
            <th><?= t('Color') ?></th>
            <th><?= t('Label') ?></th>
            <th><?= t('Background') ?></th>
            <th><?= t('Border') ?></th>
            <th><?= t('Selectable?') ?></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($colors as $color_id => $color) { ?>
    <tr>
        <td><div data-color-id="<?= $color_id ?>" class="square color-<?= $color_id ?>" ></div></td>
        <td><?= $this->form->text($color_id.'_name', $color, $errors) ?></td>
        <td><?= $this->form->text($color_id.'_background', $color, $errors) ?></td>
        <td><?= $this->form->text($color_id.'_border', $color, $errors) ?></td>
        <td><?= $this->form->checkbox($color_id.'_is_usable', '', 1, $color['is_usable']) ?></td>
    </tr>
    <? } ?>
    </tbody>
    </table>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>