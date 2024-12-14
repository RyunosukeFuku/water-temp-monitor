<?php
//
// require_once '/var/www/html/vendor/autoload.php';
// use Dotenv\Dotenv;
// $dotenv = Dotenv::createImmutable(__DIR__ . '/..');  // 親ディレクトリ（ルート）の.envを読み込む
// $dotenv->load()

// データベース接続
$pdo = new PDO('mysql:host=db;dbname=temperature_db', 'user', 'password');

// 1ページに表示するデータ数
$items_per_page = 24;

// 現在のページ番号を取得（デフォルトは1）
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 表示開始位置を計算
$offset = ($page - 1) * $items_per_page;

// データ取得（ページング対応）
$stmt = $pdo->prepare("SELECT * FROM measurements ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$measurements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 全体のデータ件数を取得
$total_stmt = $pdo->query("SELECT COUNT(*) FROM measurements");
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// 日付を取得（デフォルトは現在の日付）
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// 指定された日付の24時間の温度データを取得
$graph_stmt = $pdo->prepare("
    SELECT 
        temperature, 
        DATE_FORMAT(timestamp, '%H:%i') as time 
    FROM 
        measurements 
    WHERE 
        DATE(timestamp) = :date 
    ORDER BY 
        timestamp ASC
");
$graph_stmt->execute(['date' => $date]);
$graph_data = $graph_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<style>
        /* グラフ全体のコンテナサイズを制御 */
        #chart-container {
            width: 800px; /* 幅を小さく設定 */
            height: 500px; /* 高さを小さく設定 */
            margin: left; /* 中央寄せ */
        }

        canvas {
            max-width: 100%; /* レスポンシブ対応 */
            max-height: 100%;
        }
    </style>
    <title>Temperature Records</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Temperature Records for <?= htmlspecialchars($date) ?></h1>

    <!-- 日付選択フォーム -->
    <form method="get">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>">
        <button type="submit">Show</button>
    </form>

    <!-- 折れ線グラフを表示 -->
    <div id="chart-container">
        <canvas id="temperatureChart"></canvas>
    </div>
    <script>
        // PHPデータをJavaScriptで使用できる形に変換
        const graphLabels = <?= json_encode(array_column($graph_data, 'time')) ?>;
        const graphData = <?= json_encode(array_column($graph_data, 'temperature')) ?>;

        // Chart.jsの設定
        const ctx = document.getElementById('temperatureChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: graphLabels,
                datasets: [{
                    label: 'Temperature (°C)',
                    data: graphData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1,
                    tension: 0.3 // 曲線を少し滑らかに
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time (24 Hours)'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Temperature (°C)'
                        }
                    }
                }
            }
        });
    </script>

    <!-- データテーブル -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Temperature</th>
            <th>Timestamp</th>
            <th>Weather</th>
            <th>Notes</th>
            <th>Edit</th>
        </tr>
        <?php foreach ($measurements as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['temperature'] ?> °C</td>
            <td><?= $row['timestamp'] ?></td>
            <td><?= $row['weather'] ?></td>
            <td><?= $row['notes'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- ページングリンク -->
    <div>
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>
</body>
</html>
