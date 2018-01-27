<?php

namespace Schema;

require_once __DIR__.'/Migration.php';

use PDO;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

const VERSION = 1;
const SQLFILE = __DIR__.'/Sql/mssql_version_1.sql';

function strip_sqlcomment ($string = '') {
    $RXSQLComments = '@(--[^\r\n]*)|(\#[^\r\n]*)|(/\*[\w\W]*?(?=\*/)\*/)@ms';
    return (($string == '') ?  '' : preg_replace( $RXSQLComments, '', $string ));
}

function version_1(PDO $pdo)
{
    $sql = file_get_contents(SQLFILE);  
    $sql = iconv("UTF-16", "UTF-8//IGNORE", $sql);    
    $sql = strip_sqlcomment($sql);

    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    
    $pdo->beginTransaction();
    
    $cmd='';
    
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $sql) as $line){
     $line=trim($line);
     if ($line!=""){
        if ($line=="GO") { 
            $pdo->exec($cmd);
            $cmd='';
       } else {
           $cmd.=$line."\n";  
       } 
     }
    } 
    
    $pdo->commit();

}
