<?php
require __DIR__ . '/../vendor/autoload.php';

$phpsql = new GamerboyTR\PhpSql();

$mysqli = $phpsql->connect();