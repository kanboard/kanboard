<tr class="board-swimlane-columns-first">
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header board-column-header-first board-column-header-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">

        <!-- column in collapsed mode -->
        <div class="board-column-collapsed">
        </div>

        <!-- column in expanded mode -->
        <div class="board-column-expanded">
            <span class="board-column-title">
            </span>

            <span class="pull-right">
            </span>
        </div>

    </th>
    <?php endforeach ?>
</tr>
