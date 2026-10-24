
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Aicontro extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        // Send only the current user's prompt
        $messages = [
            [
                'role' => 'user',
                'content' => $request->input('prompt'),
            ]
        ];

        $response = Http::withToken(config('__groq_api_key__'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-20b',
                'messages' => $messages,
            ]);

        // Handle API errors
        if ($response->failed()) {
            return response()->json([
                'error' => 'Unable to get a response from the AI.',
                'details' => $response->json('error.message'),
            ], 502);
        }

        $assistantReply = $response->json('choices.0.message.content');

        return response()->json([
            'reply' => $assistantReply,
        ]);
    }
}
