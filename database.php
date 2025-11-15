<?php
session_start();
try {
    $host = 'localhost';
    $dbname = 'geoias_db';
    $username = 'root';
    $password = '06021998Dinki@';
//live.....................................
    // $host = 'localhost';
    // $dbname = 'smartgen_geoias';
    // $username = 'smartgen_geoias';
    // $password = 'EPB9cWVdbd8cQmtc3kQVgfgfgfgvdsj';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . $e->getMessage()
    ]));
}
