<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    private string $apiUrl;
    private string $user;
    private string $pass;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.openwebui.url', 'https://apikat.katrix.com.ar'), '/');
        $this->user   = config('services.openwebui.user', 'apikat');
        $this->pass   = config('services.openwebui.pass', '');
    }

    /** GET /chatbot/models */
    public function models()
    {
        try {
            $response = Http::withBasicAuth($this->user, $this->pass)
                ->withoutVerifying()
                ->timeout(15)
                ->get("{$this->apiUrl}/api/tags");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            \Log::error('Chatbot models error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'models' => []], 500);
        }
    }

    /** POST /chatbot/chat — respuesta completa (sin streaming para compatibilidad XAMPP) */
    public function chat(Request $request)
    {
        $request->validate([
            'model'    => 'required|string',
            'messages' => 'required|array',
        ]);

        try {
            // Usamos el endpoint Ollama nativo que sabemos que funciona en este servidor
            $response = Http::withBasicAuth($this->user, $this->pass)
                ->withoutVerifying()
                ->timeout(120)
                ->post("{$this->apiUrl}/api/chat", [
                    'model'    => $request->model,
                    'messages' => $request->messages,
                    'stream'   => false,
                ]);

            $json = $response->json();
            \Log::info('Chatbot response: ' . json_encode($json));

            return response()->json($json, $response->status());
        } catch (\Exception $e) {
            \Log::error('Chatbot chat error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
