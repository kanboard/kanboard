<div class="color-picker">
<?php foreach ($colors_list as $color_id => $color_name): ?>
    <div
        data-color-id="<?= $color_id ?>"
        class="color-square color-<?= $color_id ?> <?= isset($values['color_id']) && $values['color_id'] === $color_id ? 'color-square-selected' : '' ?>"
        title="<?= $this->e($color_name) ?>">
    </div>
<?php endforeach ?>
</div>

<?= $this->form->hidden('color_id', $values) ?>