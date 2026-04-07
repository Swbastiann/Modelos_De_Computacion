<?php
$hostDB = 'db';
$nameDB = 'users_db';
$userDB = 'Sebastiann';
$pwDB = '1234';

try {
    $hostPDO = "mysql:host=$hostDB;dbname=$nameDB;charset=utf8";
    $myPDO = new PDO($hostPDO, $userDB, $pwDB);
    $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error conexión DB: " . $e->getMessage()
    ]);
    exit;
}
?>