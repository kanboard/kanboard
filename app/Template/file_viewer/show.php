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
        <?php if ($officePreviewError !== ''): ?>
            <p class="alert alert-error"><?= $this->text->e($officePreviewError) ?></p>
        <?php elseif ($officePreview['type'] === 'document'): ?>
            <article class="markdown office-document">
                <p><?= t('Text-only preview; document formatting and images are not rendered.') ?></p>
                <?php foreach ($officePreview['paragraphs'] as $paragraph): ?>
                    <p><?= $this->text->e($paragraph) ?></p>
                <?php endforeach ?>
                <?php if ($officePreview['truncated']): ?>
                    <p><?= t('The preview was truncated because the document is too long.') ?></p>
                <?php endif ?>
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
                <?php if ($sheet['truncated']): ?>
                    <p><?= t('The preview was truncated to the first 500 rows and 30 columns.') ?></p>
                <?php endif ?>
            <?php endforeach ?>
            <?php if ($officePreview['truncated']): ?>
                <p><?= t('Only the first 10 worksheets are shown.') ?></p>
            <?php endif ?>
        <?php elseif ($officePreview['type'] === 'presentation'): ?>
            <?php foreach ($officePreview['slides'] as $slide): ?>
                <section class="panel">
                    <h3><?= t('Slide %d', $slide['number']) ?></h3>
                    <?php foreach ($slide['paragraphs'] as $paragraph): ?>
                        <p><?= $this->text->e($paragraph) ?></p>
                    <?php endforeach ?>
                </section>
            <?php endforeach ?>
            <?php if ($officePreview['truncated']): ?>
                <p><?= t('The preview was truncated to the first 500 slides.') ?></p>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
</div>
