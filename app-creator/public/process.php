<?php
require '../vendor/autoload.php';  // Ajusta la ruta según la ubicación de tu archivo autoload.php
use OpenAI\Client as OpenAIClient;

function connectDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "app_creator";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}

function processPrompt($prompt) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT response FROM commands WHERE command = ?");
    $stmt->bind_param("s", $prompt);
    $stmt->execute();
    $stmt->bind_result($response);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if ($response) {
        return "Comando recibido: " . htmlspecialchars($prompt) . "<br>" . $response;
    } else {
        return callChatGPT($prompt);
    }
}

function callChatGPT($prompt) {
    $apiKey = 'sk-proj-ms1Qq7wUEBDtXD4H0z7ZT3BlbkFJIaMyfhzPEUmEiBfgiyEN';
    $client = new OpenAIClient($apiKey);

    try {
        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 150,
        ]);

        $responseText = $result['choices'][0]['text'];
        if (strpos(strtolower($prompt), 'crear app android') !== false) {
            triggerJenkinsJob('android-job-name');
        } elseif (strpos(strtolower($prompt), 'crear app ios') !== false) {
            triggerJenkinsJob('ios-job-name');
        }

        return "Comando recibido: " . htmlspecialchars($prompt) . "<br>Respuesta de ChatGPT:<br>" . htmlspecialchars($responseText);
    } catch (Exception $e) {
        return "Error al llamar a la API de ChatGPT: " . $e->getMessage();
    }
}

function triggerJenkinsJob($jobName) {
    $jenkinsUrl = 'http://jenkins.yourdomain.com';
    $jenkinsUser = 'your-jenkins-username';
    $jenkinsToken = 'your-jenkins-api-token';

    $url = $jenkinsUrl . '/job/' . $jobName . '/build';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, "$jenkinsUser:$jenkinsToken");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "Error al desencadenar el trabajo de Jenkins: " . curl_error($ch);
    }

    curl_close($ch);
    return $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo processPrompt($_POST['prompt']);
}
?>
