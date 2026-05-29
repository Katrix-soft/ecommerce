<?php
/**
 * Chatbot Proxy — usa endpoints nativos de Ollama
 * GET  /chatbot-proxy.php?action=models  →  /api/tags
 * POST /chatbot-proxy.php?action=chat    →  /api/chat
 *
 * Protección: solo acepta requests del mismo dominio (validación Referer/Host).
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Leer credenciales del .env
$envFile = dirname(__DIR__) . '/.env';
$config  = [
    'url'  => 'https://apikat.katrix.com.ar',
    'user' => 'apikat',
    'pass' => '',
];

if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val, " \t\n\r\0\x0B\"'");
        match($key) {
            'OPENWEBUI_URL'  => $config['url']  = $val,
            'OPENWEBUI_USER' => $config['user'] = $val,
            'OPENWEBUI_PASS' => $config['pass'] = $val,
            default          => null,
        };
    }
}

$apiUrl   = rtrim($config['url'], '/');
$userPass = $config['user'] . ':' . $config['pass'];
$action   = $_GET['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'chat' : 'models');

// Helper cURL
function doRequest(string $url, ?string $userPass, ?array $payload = null): array {
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ];
    if ($userPass)  $opts[CURLOPT_USERPWD]    = $userPass;
    if ($payload !== null) {
        $opts[CURLOPT_POST]      = true;
        $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
    }
    curl_setopt_array($ch, $opts);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['body' => $res, 'code' => $code, 'error' => $err];
}

// ─── TEST ENDPOINTS ────────────────────────────────────────────
if ($action === 'test') {
    $tests = [
        '/api/tags',
        '/api/chat',
        '/api/generate',
        '/ollama/api/chat',
        '/api/chat/completions'
    ];
    $results = [];
    foreach ($tests as $ep) {
        $r = doRequest($apiUrl . $ep, $userPass, $ep === '/api/tags' ? null : ['model'=>'llama3', 'messages'=>[['role'=>'user','content'=>'hi']]]);
        $results[$ep] = ['code' => $r['code'], 'body' => substr($r['body'], 0, 150)];
    }
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

// ─── GET models ──────────────────────────────────────────────
if ($action === 'models') {
    $r = doRequest("{$apiUrl}/api/tags", $userPass);
    if ($r['error']) { http_response_code(500); echo json_encode(['error' => $r['error'], 'models' => []]); exit; }
    http_response_code($r['code']);
    echo $r['body'];
    exit;
}

// ─── POST chat ───────────────────────────────────────────────
if ($action === 'chat' || $action === 'chat2') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body || empty($body['model']) || empty($body['messages'])) {
        http_response_code(422);
        echo json_encode(['error' => 'Faltan model o messages']);
        exit;
    }

    $model    = $body['model'];
    $messages = $body['messages'];

    // Endpoint nativo Ollama /api/chat con basic auth (igual que /api/tags que funciona)
    $r = doRequest("{$apiUrl}/api/chat", $userPass, [
        'model'    => $model,
        'messages' => $messages,
        'stream'   => false,
    ]);

    if ($r['error']) {
        http_response_code(500);
        echo json_encode(['error' => 'cURL: ' . $r['error']]);
        exit;
    }

    if ($r['code'] < 200 || $r['code'] >= 300) {
        http_response_code($r['code']);
        echo json_encode(['error' => "HTTP {$r['code']}: " . substr($r['body'], 0, 300)]);
        exit;
    }

    $json = json_decode($r['body'], true);
    if (!$json) {
        http_response_code(500);
        echo json_encode(['error' => 'JSON inválido: ' . substr($r['body'], 0, 300)]);
        exit;
    }

    // Respuesta Ollama: { message: { role, content } }
    $content = $json['message']['content'] ?? $json['response'] ?? null;

    if ($content === null) {
        echo json_encode(['error' => 'Sin content en respuesta', 'raw' => $json]);
        exit;
    }

    echo json_encode(['content' => $content]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Acción no válida']);
