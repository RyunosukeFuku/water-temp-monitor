<?php
// env
require_once '/var/www/html/vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// 天気APIを使用して天気情報を取得する関数
function getWeather($location = "Tokyo") {
    $apiKey = $_ENV['API_KEY'];
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$location&appid=$apiKey&units=metric";

    $response = @file_get_contents($url);
    if ($response === FALSE) {
        die("Error: Unable to fetch data from OpenWeatherMap. Check API key or server connectivity.");
    }

    $weatherData = json_decode($response, true);
    if (isset($weatherData['cod']) && $weatherData['cod'] == 200) {
        return [
            'temp' => $weatherData['main']['temp'],
            'description' => $weatherData['weather'][0]['description']
        ];
    } else {
        die("Error: Invalid response from OpenWeatherMap. Check API key or query parameters.");
    }
}
// 関数を実行して結果を表示
$weather = getWeather();
print_r($weather);
?>
