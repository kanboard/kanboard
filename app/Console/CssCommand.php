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
 * Class CssCommand
 *
 * @package Kanboard\Console
 * @author  Frederic Guillot
 */
class CssCommand extends BaseCommand
{
    const CSS_SRC_PATH = 'assets/css/src/';
    const CSS_VENDOR_PATH = 'assets/vendor/';
    const CSS_DIST_PATH = 'assets/css/';

    private $appFiles = [
        'variables.css',
        'base.css',
        'links.css',
        'titles.css',
        'table.css',
        'table_drag_and_drop.css',
        'table_list.css',
        'form.css',
        'input_addon.css',
        'icon.css',
        'alert.css',
        'button.css',
        'tooltip.css',
        'dropdown.css',
        'accordion.css',
        'select_dropdown.css',
        'suggest_menu.css',
        'modal.css',
        'pagination.css',
        'header.css',
        'logo.css',
        'page_header.css',
        'sidebar.css',
        'avatar.css',
        'file_upload.css',
        'thumbnails.css',
        'color_picker.css',
        'filter_box.css',
        'project.css',
        'views.css',
        'dashboard.css',
        'board.css',
        'task_board.css',
        'task_icons.css',
        'task_category.css',
        'task_date.css',
        'task_tags.css',
        'task_summary.css',
        'task_form.css',
        'comment.css',
        'subtasks.css',
        'task_list.css',
        'task_links.css',
        'text_editor.css',
        'markdown.css',
        'panel.css',
        'activity_stream.css',
        'user_mention.css',
        'slideshow.css',
        'list_items.css',
        'bulk_change.css',
    ];

    private $printFiles = [
        'print.css',
    ];

    private $vendorFiles = [
        self::CSS_VENDOR_PATH.'jquery-ui/jquery-ui.min.css',
        self::CSS_VENDOR_PATH.'jqueryui-timepicker-addon/jquery-ui-timepicker-addon.min.css',
        self::CSS_VENDOR_PATH.'select2/css/select2.min.css',
        self::CSS_VENDOR_PATH.'font-awesome/css/font-awesome.min.css',
        self::CSS_VENDOR_PATH.'c3/c3.min.css',
    ];

    protected function configure()
    {
        $this
            ->setName('css')
            ->setDescription('Minify CSS files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->minifyFiles(self::CSS_SRC_PATH, $this->appFiles, 'app.min.css');
        $this->minifyFiles(self::CSS_SRC_PATH, $this->printFiles, 'print.min.css');

        $vendorBundle = concat_files($this->vendorFiles);
        file_put_contents('assets/css/vendor.min.css', $vendorBundle);
    }

    private function minifyFiles($folder, array $files, $destination)
    {
        $minifier = new Minify\CSS();

        foreach ($files as $file) {
            $filename = $folder.$file;
            if (! file_exists($filename)) {
                die("$filename not found\n");
            }
            $minifier->add($filename);
        }

        $minifier->minify(self::CSS_DIST_PATH . $destination);
    }
}
