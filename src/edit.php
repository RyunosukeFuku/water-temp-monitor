<?php
// データベース接続
$pdo = new PDO('mysql:host=db;dbname=temperature_db', 'user', 'password');

// 編集対象のIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    die('Invalid ID');
}

// データ取得
$stmt = $pdo->prepare("SELECT * FROM measurements WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die('Record not found');
}

// フォーム送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temperature = $_POST['temperature'];
    $timestamp = $_POST['timestamp'];
    $weather = $_POST['weather'];
    $notes = $_POST['notes'];

    // データ更新
    $update_stmt = $pdo->prepare("
        UPDATE measurements 
        SET temperature = :temperature, timestamp = :timestamp, weather = :weather, notes = :notes
        WHERE id = :id
    ");
    $update_stmt->execute([
        ':temperature' => $temperature,
        ':timestamp' => $timestamp,
        ':weather' => $weather,
        ':notes' => $notes,
        ':id' => $id
    ]);

    // リダイレクトして元のページに戻る
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Record</title>
</head>
<body>
    <h1>Edit Record</h1>
    <form method="post">
        <label>
            Temperature:
            <input type="text" name="temperature" value="<?= htmlspecialchars($record['temperature']) ?>">
        </label>
        <br>
        <label>
            Timestamp:
            <input type="text" name="timestamp" value="<?= htmlspecialchars($record['timestamp']) ?>">
        </label>
        <br>
        <label>
            Weather:
            <input type="text" name="weather" value="<?= htmlspecialchars($record['weather']) ?>">
        </label>
        <br>
        <label>
            Notes:
            <textarea name="notes"><?= htmlspecialchars($record['notes']) ?></textarea>
        </label>
        <br>
        <button type="submit">Save</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
