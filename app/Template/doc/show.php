<section id="main">
    <div class="page-header">
        <ul class="btn-group">
            <li>
                <?= $this->url->buttonLink('<fa-life-ring>' . t('Table of contents'), 'doc', 'show', array('file' => 'index')) ?>
            </li>
        </ul>
    </div>
    <div class="markdown documentation">
        <?= $content ?>
    </div>
</section>
