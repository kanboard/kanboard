<?php

$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
$test = crypt("password", $hash);
$pass = $test == $hash;

echo "Test for functionality of compat library: " . ($pass ? "Pass" : "Fail");
echo "\n";