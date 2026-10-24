<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class Aicontro extends Controller
{
   public function index(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);
        
       $messages = session('chat_history', []);

// Add current user message
$messages[] = [
    'role' => 'user',
    'content' => $request->input('prompt')
];

$response = Http::withToken('__groq_api_key__')
    ->post('https://api.groq.com/openai/v1/chat/completions', [
        'model' => 'openai/gpt-oss-20b',
        'messages' => $messages
    ]);

$data = $response->json();

// Save assistant reply
$assistantReply = $data['choices'][0]['message']['content'];

$messages[] = [
    'role' => 'assistant',
    'content' => $assistantReply
];

session(['chat_history' => $messages]);

return response()->json([
    'reply' => $assistantReply
]);
    }
}
