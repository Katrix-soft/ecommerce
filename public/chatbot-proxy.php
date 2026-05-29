<?php
/**
 * Chatbot Proxy — OpenWebUI compatible (OpenAI API)
 *
 * GET  /chatbot-proxy.php?action=models  →  /api/models        (Bearer token)
 * POST /chatbot-proxy.php?action=chat    →  /api/chat/completions (Bearer token)
 *
 * OpenWebUI expone API OpenAI-compatible, NO Ollama nativa.
 * Auth: Bearer JWT (OPENWEBUI_TOKEN), con fallback a Basic Auth.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ─── Leer configuración del .env ─────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
$config  = [
    'url'   => 'https://apikat.katrix.com.ar',
    'token' => '',   // OPENWEBUI_TOKEN (JWT Bearer) — preferido
    'user'  => 'apikat',
    'pass'  => '',   // fallback Basic Auth
];

if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val, " \t\n\r\0\x0B\"'");
        match($key) {
            'OPENWEBUI_URL'   => $config['url']   = $val,
            'OPENWEBUI_TOKEN' => $config['token']  = $val,
            'OPENWEBUI_USER'  => $config['user']   = $val,
            'OPENWEBUI_PASS'  => $config['pass']   = $val,
            default           => null,
        };
    }
}

$apiUrl = rtrim($config['url'], '/');
$action = $_GET['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'chat' : 'models');

// ─── Helper cURL con Bearer token ────────────────────────────────────────────
function doRequest(string $url, array $config, ?array $payload = null): array {
    $ch = curl_init($url);

    // Elegir autenticación: Bearer JWT preferido, Basic Auth como fallback
    if (!empty($config['token'])) {
        $authHeader = 'Authorization: Bearer ' . $config['token'];
    } else {
        $authHeader = 'Authorization: Basic ' . base64_encode($config['user'] . ':' . $config['pass']);
    }

    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            $authHeader,
        ],
    ];

    if ($payload !== null) {
        $opts[CURLOPT_POST]       = true;
        $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
    }

    curl_setopt_array($ch, $opts);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['body' => $res, 'code' => $code, 'error' => $err];
}

// ─── TEST: verificar endpoints disponibles ───────────────────────────────────
if ($action === 'test') {
    $endpoints = [
        'models_openai'   => ['GET',  '/api/models'],
        'models_tags'     => ['GET',  '/ollama/api/tags'],
        'chat_openai'     => ['POST', '/api/chat/completions'],
        'chat_ollama'     => ['POST', '/ollama/api/chat'],
    ];
    $results = [];
    foreach ($endpoints as $name => [$method, $ep]) {
        $payload = null;
        if ($method === 'POST') {
            $payload = [
                'model'    => 'qwen2.5:1.5b',
                'messages' => [['role' => 'user', 'content' => 'hi']],
                'stream'   => false,
            ];
        }
        $r = doRequest($apiUrl . $ep, $config, $payload);
        $results[$name] = [
            'endpoint' => $ep,
            'code'     => $r['code'],
            'error'    => $r['error'],
            'body'     => substr($r['body'] ?: '', 0, 200),
        ];
    }
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

// ─── GET models ──────────────────────────────────────────────────────────────
// OpenWebUI: GET /api/models  →  { data: [ { id: "model_name", ... }, ... ] }
// Ollama:    GET /api/tags    →  { models: [ { name: "model_name", ... }, ... ] }
if ($action === 'models') {
    $r = doRequest("{$apiUrl}/api/models", $config);

    if ($r['error']) {
        http_response_code(500);
        echo json_encode(['error' => $r['error'], 'models' => []]);
        exit;
    }

    if ($r['code'] < 200 || $r['code'] >= 300) {
        // Fallback: intentar con endpoint Ollama nativo vía proxy de OpenWebUI
        $r2 = doRequest("{$apiUrl}/ollama/api/tags", $config);
        if ($r2['code'] >= 200 && $r2['code'] < 300) {
            // Normalizar formato Ollama → OpenAI
            $ollama = json_decode($r2['body'], true);
            $models = array_map(fn($m) => ['id' => $m['name'], 'name' => $m['name']], $ollama['models'] ?? []);
            echo json_encode(['models' => $models]);
            exit;
        }
        http_response_code($r['code']);
        echo json_encode(['error' => "HTTP {$r['code']}", 'models' => []]);
        exit;
    }

    // Respuesta OpenAI: { data: [...] } → normalizar a { models: [...] }
    $json   = json_decode($r['body'], true);
    $models = [];
    if (isset($json['data'])) {
        // Formato OpenAI
        foreach ($json['data'] as $m) {
            $models[] = ['id' => $m['id'], 'name' => $m['id']];
        }
    } elseif (isset($json['models'])) {
        // Formato Ollama directo
        $models = array_map(fn($m) => ['id' => $m['name'], 'name' => $m['name']], $json['models']);
    }

    echo json_encode(['models' => $models]);
    exit;
}

// ─── POST chat ───────────────────────────────────────────────────────────────
// OpenWebUI: POST /api/chat/completions (OpenAI compatible)
// Respuesta: { choices: [ { message: { content: "..." } } ] }
if ($action === 'chat' || $action === 'chat2') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body || empty($body['model']) || empty($body['messages'])) {
        http_response_code(422);
        echo json_encode(['error' => 'Faltan model o messages']);
        exit;
    }

    $r = doRequest("{$apiUrl}/api/chat/completions", $config, [
        'model'    => $body['model'],
        'messages' => $body['messages'],
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

    // Formato OpenAI: choices[0].message.content
    // Fallback formato Ollama: message.content o response
    $content =
        $json['choices'][0]['message']['content'] ??
        $json['message']['content'] ??
        $json['response'] ??
        null;

    if ($content === null) {
        echo json_encode(['error' => 'Sin content en respuesta', 'raw' => $json]);
        exit;
    }

    echo json_encode(['content' => $content]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Acción no válida']);
