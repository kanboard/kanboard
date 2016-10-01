<select id="board-selector"
        class="chosen-select select-auto-redirect"
        tabindex="-1"
        data-search-threshold="0"
        data-notfound="<?= t('No results match:') ?>"
        data-placeholder="<?= t('Display another project') ?>"
        data-redirect-regex="PROJECT_ID"
        data-redirect-url="<?= $this->url->href('BoardViewController', 'show', array('project_id' => 'PROJECT_ID')) ?>">
    <option value=""></option>
    <?php foreach ($board_selector as $board_id => $board_name): ?>
        <option value="<?= $board_id ?>"><?= $this->text->e($board_name) ?></option>
    <?php endforeach ?>
</select>
