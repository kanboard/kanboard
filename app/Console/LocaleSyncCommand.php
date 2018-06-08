<?php

namespace Kanboard\Console;

use DirectoryIterator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocaleSyncCommand extends BaseCommand
{
    const REF_LOCALE = 'fr_FR';

    protected function configure()
    {
        $this
            ->setName('locale:sync')
            ->setDescription('Synchronize all translations based on the '.self::REF_LOCALE.' locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reference_file = APP_DIR.DIRECTORY_SEPARATOR.'Locale'.DIRECTORY_SEPARATOR.self::REF_LOCALE.DIRECTORY_SEPARATOR.'translations.php';
        $reference = include $reference_file;

        foreach (new DirectoryIterator(APP_DIR.DIRECTORY_SEPARATOR.'Locale') as $fileInfo) {
            if (! $fileInfo->isDot() && $fileInfo->isDir() && $fileInfo->getFilename() !== self::REF_LOCALE) {
                $filename = APP_DIR.DIRECTORY_SEPARATOR.'Locale'.DIRECTORY_SEPARATOR.$fileInfo->getFilename().DIRECTORY_SEPARATOR.'translations.php';
                echo $fileInfo->getFilename().' ('.$filename.')'.PHP_EOL;

                file_put_contents($filename, $this->updateFile($reference, $filename));
            }
        }
    }

    public function updateFile(array $reference, $outdated_file)
    {
        $outdated = include $outdated_file;

        $output = '<?php'.PHP_EOL.PHP_EOL;
        $output .= 'return array('.PHP_EOL;

        foreach ($reference as $key => $value) {
            if (! empty($outdated[$key])) {
                $output .= "    '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $outdated[$key])."',\n";
            } else {
                $output .= "    // '".str_replace("'", "\'", $key)."' => '',\n";
            }
        }

        $output .= ");\n";
        return $output;
    }
}
