<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class CustomerMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        if ($messages->isNotEmpty()) {
            Message::where('user_id', $user->id)
                ->where('sender_role', 'admin')
                ->where('is_read_by_user', false)
                ->update(['is_read_by_user' => true]);
        }

        return view('shop.messages.index', [
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'user_id' => $user->id,
            'sender_role' => 'user',
            'body' => $data['body'],
        ]);

        return redirect()->route('shop.messages.index');
    }
}
