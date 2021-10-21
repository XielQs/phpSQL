<?php
require __DIR__."/phpsql.class.php";

$phpsql = new GamerboyTR\PhpSql();
$phpsql->setMysqli();
$phpsql->setDatabase("phpsql");

print_r($phpsql->select("*", "users"));

?>
<!DOCTYPE html>
<html lang="tr-TR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhpSql - Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-size: 2.5rem;
    }

    p {
        margin-bottom: 0;
    }
    </style>
</head>

<body>
    <p>Mysqli Status : <?=$phpsql->getStatus()['success'] ? "Active" : "Error"?></p>
    <p>Mysqli Host : <?=$phpsql->getMysqliDetails()['host']?></p>
    <p>Mysqli User : <?=$phpsql->getMysqliDetails()['user']?></p>
    <p>Mysqli Passwrd : <?=$phpsql->getMysqliDetails()['password']?></p>
    <p>Mysqli Database : <?=$phpsql->getMysqliDetails()['database']?></p>
</body>

</html>