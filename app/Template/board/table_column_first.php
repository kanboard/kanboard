<!-- FIRST column titles .. setting up the "grid" -->

<tr class="board-swimlane-columns-<?= $swimlane['id'] ?> ">
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header-first board-column-header-first-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">

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
