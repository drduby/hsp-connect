<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->take(30)
            ->get()
            ->map(fn ($n) => match (true) {
                isset($n->data['liker_nickname']) => [
                    'id' => $n->id,
                    'text' => __('ui.notifications.liked', ['liker' => $n->data['liker_nickname'], 'title' => $n->data['post_title']]),
                    'time' => $n->created_at->diffForHumans(),
                    'read' => ! is_null($n->read_at),
                    'icon' => '❤️',
                ],
                isset($n->data['commenter_nickname']) => [
                    'id' => $n->id,
                    'text' => __('ui.notifications.commented', ['commenter' => $n->data['commenter_nickname'], 'title' => $n->data['post_title']]),
                    'time' => $n->created_at->diffForHumans(),
                    'read' => ! is_null($n->read_at),
                    'icon' => '💬',
                ],
                isset($n->data['rater_nickname']) => [
                    'id' => $n->id,
                    'text' => __('ui.notifications.rated', [
                        'rater' => $n->data['rater_nickname'],
                        'title' => $n->data['post_title'],
                        'stars' => str_repeat('★', $n->data['rating']).str_repeat('☆', 5 - $n->data['rating']),
                    ]),
                    'time' => $n->created_at->diffForHumans(),
                    'read' => ! is_null($n->read_at),
                    'icon' => '⭐',
                ],
                default => null,
            })
            ->filter()
            ->values();

        return response()->json($notifications);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return response()->json(['ok' => true]);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return response()->json(['ok' => true]);
    }
}
