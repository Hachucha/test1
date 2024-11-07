<?php

// Параметры подключения к БД
$host = 'localhost';
$dbname = 'testvova_db';
$username = 'testvova_db_usr';
$password = 'TPLNhKzksoG55KG7';

// Установить соединение с БД
function connectToDb() {
    global $host, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}