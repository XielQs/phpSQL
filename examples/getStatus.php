<?php
require __DIR__ . '/../vendor/autoload.php';

$phpsql = new GamerboyTR\phpSQL();

if (!$phpsql->getStatus()) die("phpSQL Not Working !");
