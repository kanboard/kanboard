<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->url->button('fa-life-ring', t('Table of contents'), 'doc', 'show', array('file' => 'index')) ?>
            </li>
        </ul>
    </div>
    <div class="markdown documentation">
        <?= $content ?>
    </div>
</section>
