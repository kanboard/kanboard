<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->url->icon('life-ring', t('Table of contents'), 'DocumentationController', 'show', array('file' => 'index')) ?>
            </li>
        </ul>
    </div>
    <div class="markdown documentation">
        <?= $content ?>
    </div>
</section>
