<?php
session_start();
try {
    $host = 'localhost';
    $dbname = 'school_website';
    $username = 'root';
    $password = '';
//live.....................................
    // $host = 'localhost';
    // $dbname = 'margdarshan_db';
    // $username = 'root';
    // $password = '06021998Dinki@';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . $e->getMessage()
    ]));
}
$production = false; // Set to false for development environment

function getSeoData() {
    global $production; // function ke andar variable use karne ke liye global
    if($production) {
        echo "/";
    }
}
