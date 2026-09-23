<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Services\Chatbot\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function ask(Request $request, ChatbotService $bot)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $result = $bot->reply($data['message'], $request->user());

        // Riwayat pertanyaan dipakai admin untuk melihat apa yang belum bisa dijawab bot.
        ChatLog::create([
            'user_id' => $request->user()?->id,
            'question' => mb_substr($data['message'], 0, 500),
            'intent' => $result['intent'],
            'faq_id' => $result['faq_id'],
            'answered' => $result['answered'],
        ]);

        return response()->json($result);
    }
}
