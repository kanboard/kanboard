<div class="page-header">
    <h2><?= $this->text->e($file['name']) ?></h2>
</div>
<div class="file-viewer">
    <?php if ($file['is_image']): ?>
        <img src="<?= $this->url->href('FileViewerController', 'image', $params) ?>" alt="<?= $this->text->e($file['name']) ?>">
    <?php elseif ($type === 'markdown'): ?>
        <article class="markdown">
            <?= $this->text->markdown($content) ?>
        </article>
    <?php elseif ($type === 'text'): ?>
        <pre><?= $this->text->e($content) ?></pre>
    <?php elseif ($type === 'office'): ?>
        <?php if ($officePreview['type'] === 'document'): ?>
            <article class="markdown office-document">
                <?php foreach ($officePreview['paragraphs'] as $paragraph): ?>
                    <p><?= $this->text->e($paragraph) ?></p>
                <?php endforeach ?>
            </article>
        <?php elseif ($officePreview['type'] === 'spreadsheet'): ?>
            <?php foreach ($officePreview['sheets'] as $sheet): ?>
                <h3><?= $this->text->e($sheet['name']) ?></h3>
                <table class="table-striped table-scrolling">
                    <?php foreach ($sheet['rows'] as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= $this->text->e($cell) ?></td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endforeach ?>
        <?php elseif ($officePreview['type'] === 'presentation'): ?>
            <?php foreach ($officePreview['slides'] as $slide): ?>
                <section class="panel">
                    <h3><?= t('Slide %d', $slide['number']) ?></h3>
                    <?php foreach ($slide['paragraphs'] as $paragraph): ?>
                        <p><?= $this->text->e($paragraph) ?></p>
                    <?php endforeach ?>
                </section>
            <?php endforeach ?>
        <?php endif ?>
    <?php endif ?>
</div>
