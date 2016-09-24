<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->url->link('<i class="fa fa-life-ring fa-fw"></i>' . t('Table of contents'), 'DocumentationController', 'show', array('file' => 'index')) ?>
            </li>
        </ul>
    </div>
    <div class="markdown documentation">
        <?= $content ?>
    </div>
</section>
