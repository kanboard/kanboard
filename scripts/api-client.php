#!/usr/bin/env php
<?php

require 'vendor/JsonRPC/Client.php';

if ($argc !== 3) {
    die('Usage: '.$argv[0].' <url> <token>'.PHP_EOL);
}

$client = new JsonRPC\Client($argv[1], 5, true);
$client->authentication('jsonrpc', $argv[2]);


$client->createProject('Test API');


$r = $client->getAllProjects();

var_dump($r);

