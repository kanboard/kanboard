<?php

namespace AlternativeSchema;

use PDO;

function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE test1 (column1 TEXT)');
}

function version_2(PDO $pdo)
{
    $pdo->exec('CREATE TABLE test2 (column2 TEXT)');
}
