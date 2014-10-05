<section id="main">
    <div class="page-header">
        <h2><?= t('Settings') ?></h2>
    </div>
    <section class="config-show" id="config-section">

        <?= Helper\template('config_sidebar') ?>

        <div class="config-show-main">
            <?= $config_content_for_layout ?>
        </div>
    </section>
</section>