<?php

namespace Kanboard\Console;

use MatthiasMullie\Minify;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$path = __DIR__ . '/../../libs';
require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/minify/src/Exceptions/BasicException.php';
require_once $path . '/minify/src/Exceptions/FileImportException.php';
require_once $path . '/minify/src/Exceptions/IOException.php';
require_once $path . '/path-converter/src/ConverterInterface.php';
require_once $path . '/path-converter/src/Converter.php';

/**
 * Class JsCommand
 *
 * @package Kanboard\Console
 * @author  Frederic Guillot
 */
class JsCommand extends BaseCommand
{
    const CSS_DIST_PATH = 'assets/js/';

    private $appFiles = [
        'assets/vendor/text-caret/index.js',
        'assets/js/polyfills/*.js',
        'assets/js/core/base.js',
        'assets/js/core/dom.js',
        'assets/js/core/html.js',
        'assets/js/core/http.js',
        'assets/js/core/modal.js',
        'assets/js/core/tooltip.js',
        'assets/js/core/utils.js',
        'assets/js/components/*.js',
        'assets/js/core/bootstrap.js',
        'assets/js/src/Namespace.js',
        'assets/js/src/App.js',
        'assets/js/src/BoardCollapsedMode.js',
        'assets/js/src/BoardColumnView.js',
        'assets/js/src/BoardHorizontalScrolling.js',
        'assets/js/src/BoardPolling.js',
        'assets/js/src/BoardVerticalScrolling.js',
        'assets/js/src/Column.js',
        'assets/js/src/Dropdown.js',
        'assets/js/src/Search.js',
        'assets/js/src/Swimlane.js',
        'assets/js/src/Task.js',
        'assets/js/src/BoardDragAndDrop.js',
        'assets/js/src/Bootstrap.js'
    ];

    private $vendorFiles = [
        'assets/vendor/jquery/jquery-3.4.1.min.js',
        'assets/vendor/jquery-ui/jquery-ui.min.js',
        'assets/vendor/jquery-ui/i18n/datepicker-*.js',
        'assets/vendor/jqueryui-timepicker-addon/jquery-ui-timepicker-addon.min.js',
        'assets/vendor/jqueryui-timepicker-addon/i18n/jquery-ui-timepicker-addon-i18n.min.js',
        'assets/vendor/jqueryui-touch-punch/jquery.ui.touch-punch.min.js',
        'assets/vendor/select2/js/select2.min.js',
        'assets/vendor/select2/js/i18n/*.js',
        'assets/vendor/d3/d3.min.js',
        'assets/vendor/c3/c3.min.js',
        'assets/vendor/isMobile/isMobile.min.js',
    ];

    protected function configure()
    {
        $this
            ->setName('js')
            ->setDescription('Minify Javascript files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appBundle = concat_files($this->appFiles);
        $vendorBundle = concat_files($this->vendorFiles);

        $minifier = new Minify\JS($appBundle);

        file_put_contents('assets/js/app.min.js', $minifier->minify());
        file_put_contents('assets/js/vendor.min.js', $vendorBundle);
    }
}
