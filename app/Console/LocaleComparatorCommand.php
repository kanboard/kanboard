<?php

namespace Kanboard\Console;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocaleComparatorCommand extends BaseCommand
{
    const REF_LOCALE = 'fr_FR';

    protected function configure()
    {
        $this
            ->setName('locale:compare')
            ->setDescription('Compare application translations with the '.self::REF_LOCALE.' locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $strings = array();
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_DIR));
        $it->rewind();

        while ($it->valid()) {
            if (! $it->isDot() && substr($it->key(), -4) === '.php') {
                $strings = array_merge($strings, $this->search($it->key()));
            }

            $it->next();
        }

        $this->compare(array_unique($strings));
        return 0;
    }

    public function show(array $strings)
    {
        foreach ($strings as $string) {
            echo "    '".str_replace("'", "\'", $string)."' => '',".PHP_EOL;
        }
    }

    public function compare(array $strings)
    {
        $reference_file = APP_DIR.DIRECTORY_SEPARATOR.'Locale'.DIRECTORY_SEPARATOR.self::REF_LOCALE.DIRECTORY_SEPARATOR.'translations.php';
        $reference = include $reference_file;

        echo str_repeat('#', 70).PHP_EOL;
        echo 'MISSING STRINGS'.PHP_EOL;
        echo str_repeat('#', 70).PHP_EOL;
        $this->show(array_diff($strings, array_keys($reference)));

        echo str_repeat('#', 70).PHP_EOL;
        echo 'USELESS STRINGS'.PHP_EOL;
        echo str_repeat('#', 70).PHP_EOL;
        $this->show(array_diff(array_keys($reference), $strings));
    }

    public function search($filename)
    {
        $content = file_get_contents($filename);
        $strings = array();

        if (preg_match_all('/\b[et]\s*\(\s*(\'\K.*?\')\s*[\)\,]/', $content, $matches) && isset($matches[1])) {
            $strings = $matches[1];
        }

        if (preg_match_all('/\bdt\s*\(\s*(\'\K.*?\')\s*[\)\,]/', $content, $matches) && isset($matches[1])) {
            $strings = array_merge($strings, $matches[1]);
        }

        array_walk($strings, function (&$value) {
            $value = trim($value, "'");
            $value = str_replace("\'", "'", $value);
        });

        return $strings;
    }
}
