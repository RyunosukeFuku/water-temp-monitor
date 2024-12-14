<?php
// タイムゾーンを日本時間に設定
date_default_timezone_set('Asia/Tokyo');
$pdo = new PDO('mysql:host=db;dbname=temperature_db', 'user', 'password');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
// サーバ側で日時を取得
$timestamp = date('Y-m-d H:i');
require 'weather.php';
$weather = getWeather();

$stmt = $pdo->prepare("INSERT INTO measurements (temperature, timestamp, weather, notes) VALUES (?, ?, ?, ?)");
$stmt->execute([
    $data['temperature'],
    $timestamp,
    $weather['description'],
    null
]);

echo "Data received successfully!";
?>
