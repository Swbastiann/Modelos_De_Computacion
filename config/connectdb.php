<?php
    $hostDB = '127.0.0.1';
    $nameDB = 'users_db';
    $userDB = 'Sebastiann';
    $pwDB = '1234';

    $hostPDO = "mysql:host=$hostDB;dbname=$nameDB;charset=utf8";
    $myPDO = new PDO($hostPDO, $userDB, $pwDB);
    $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $myQuery = $myPDO->prepare('SELECT * FROM users;');
    $myQuery->execute();
    $result = $myQuery->fetchAll(PDO::FETCH_ASSOC);
?>