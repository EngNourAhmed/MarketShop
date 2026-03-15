<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');

        $users = User::query()
            ->withCount(['messages as unread_messages_count' => function ($q) {
                $q->where('sender_role', 'user')
                    ->where('is_read_by_admin', false);
            }])
            ->orderBy('name')
            ->get();

        $activeUser = null;
        $messages = collect();

        if ($userId) {
            $activeUser = $users->firstWhere('id', (int) $userId);
        }

        if (!$activeUser && $users->isNotEmpty()) {
            $activeUser = $users->first();
        }

        if ($activeUser) {
            $messages = Message::where('user_id', $activeUser->id)
                ->orderBy('created_at')
                ->get();

            Message::where('user_id', $activeUser->id)
                ->where('sender_role', 'user')
                ->where('is_read_by_admin', false)
                ->update(['is_read_by_admin' => true]);
        }

        $latestMessages = collect();
        if ($users->isNotEmpty()) {
            $latestMessages = Message::query()
                ->where('sender_role', 'user')
                ->whereIn('user_id', $users->pluck('id'))
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('user_id')
                ->map(function ($group) {
                    return $group->first();
                });
        }

        return view('messages.index', [
            'users' => $users,
            'activeUser' => $activeUser,
            'messages' => $messages,
            'latestMessages' => $latestMessages,
        ]);
    }

    public function notifications(Request $request)
    {
        // Users that have at least one unread message from user -> admin
        $rows = Message::query()
            ->where('sender_role', 'user')
            ->where('is_read_by_admin', false)
            ->selectRaw('user_id, COUNT(*) as unread_count, MAX(created_at) as latest_at')
            ->groupBy('user_id')
            ->orderByDesc('latest_at')
            ->get();

        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        $latestMessages = Message::query()
            ->where('sender_role', 'user')
            ->where('is_read_by_admin', false)
            ->whereIn('user_id', $rows->pluck('user_id'))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                return $group->first();
            });

        $notifications = $rows->map(function ($row) use ($users, $latestMessages) {
            $user = $users->get($row->user_id);
            if (!$user) {
                return null;
            }

            return [
                'user' => $user,
                'unread_count' => (int) $row->unread_count,
                'latest_message' => $latestMessages->get($row->user_id),
            ];
        })->filter()->values();

        return view('messages.notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'user_id' => $data['user_id'],
            'sender_role' => 'admin',
            'body' => $data['body'],
        ]);

        // Create a customer notification for this message if we find a matching customer by email
        $user = User::find($data['user_id']);
        if ($user && $user->email) {
            $customer = Customer::where('email', $user->email)->first();
            if ($customer) {
                $notification = new CustomerNotification();
                $notification->customer_id = $customer->id;
                $notification->title = 'رسالة جديدة من الدعم';
                $notification->body = mb_substr($message->body, 0, 200);
                $notification->data = [
                    'type' => 'message',
                    'message_id' => $message->id,
                ];
                $notification->created_by = $request->user()->id ?? null;
                $notification->save();
            }
        }

        return redirect()->route('messages.index', ['user_id' => $data['user_id']]);
    }
}
