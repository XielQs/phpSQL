<?php
require __DIR__ . '/../vendor/autoload.php';

$phpsql = new GamerboyTR\PhpSql();

if(!$phpsql->getStatus()) die("PhpSql Not Working !");