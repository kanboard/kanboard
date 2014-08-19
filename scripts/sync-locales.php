#!/usr/bin/env php
<?php

$reference_lang = 'fr_FR';
$reference_file = 'app/Locales/'.$reference_lang.'/translations.php';
$reference = include $reference_file;


function update_missing_locales(array $reference, $outdated_file)
{
    $outdated = include $outdated_file;

    $output = '<?php'.PHP_EOL.PHP_EOL;
    $output .= 'return array('.PHP_EOL;

    foreach ($reference as $key => $value) {

        if (isset($outdated[$key])) {
            //$output .= "    '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $value)."',\n";
            $output .= "    '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $outdated[$key])."',\n";
        }
        else {
            //$output .= "    // '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $value)."',\n";
            $output .= "    // '".str_replace("'", "\'", $key)."' => '',\n";
        }
    }

    $output .= ");\n";
    return $output;
}


foreach (new DirectoryIterator('app/Locales') as $fileInfo) {

    if (! $fileInfo->isDot() && $fileInfo->isDir() && $fileInfo->getFilename() !== $reference_lang) {

        $filename = 'app/Locales/'.$fileInfo->getFilename().'/translations.php';

        echo $fileInfo->getFilename().' ('.$filename.')'.PHP_EOL;

        file_put_contents($filename, update_missing_locales($reference, $filename));
    }
}
