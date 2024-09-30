<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use App\Notifications\InactivePostNotification;
use App\Notifications\PostDeletionRequestNotification;
use App\Notifications\PostStatusChangedNotification;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index($drop)
    {
        $user = Auth::user();
        if ($user) {
            $notifications = $user->notifications->sortByDesc('created_at');
            if ($drop == 0) {
                return response()->json($notifications->take(50)->values());
            } else {
                return response()->json($notifications->take(5)->values());
            }
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }



    public function sendResetLinkEmail(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!empty($user)) {
            $user->remember_token = Str::random(40);
            $user->save();

            Mail::to($user->email)->send(new ResetPasswordMail($user->remember_token));

            return response()->json(['message' => 'Reset link sent to your email.']);
        }
        return response()->json(['message' => 'Unable to send reset link.'], 500);
    }





    
public function notifyStatusChange(Request $request, $id)
{
    $post = Post::findOrFail($id);
    $status = $request->input('status');
    $post->status = $status;
    $post->save();

    $postOwner = $post->user;

    $notificationData = [
        'message' => $postOwner->name.' has uploaded a post.',
            'post_id' => $post->id,
    ];

    $notification = \App\Models\Notification::create([
        'id' => (string) Str::uuid(), 
        'type' => 'PostStatusChanged',
        'data' => $notificationData,
    ]);

    $userIds = [];

    if ($status == Post::STATUS_PRIVATE) {
        $userIds = User::where('role_id', 2)
            ->where('department_id', $postOwner->department_id)
            ->where('id', '!=', $postOwner->id)
            ->pluck('id')
            ->toArray();
    } elseif ($status == Post::STATUS_VISIBLE) {
        $userIds = User::where('department_id', $postOwner->department_id)
            ->where('id', '!=', $postOwner->id)
            ->pluck('id')
            ->toArray();
    } elseif ($status == Post::STATUS_PUBLIC) {
        $userIds = User::where('id', '!=', $postOwner->id)
            ->pluck('id')
            ->toArray();
    }

    if (!empty($userIds)) {
        DB::table('notification_user')->insert(
            collect($userIds)->map(fn($userId) => [
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );
    }

    $postOwner->notify(new PostStatusChangedNotification($post, $status, 'owner'));

    return response()->json(['message' => 'Post status updated successfully']);
}


    public function notifySuperAdmin($postId)
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post = Post::findOrFail($postId);

        $superAdmins = User::where('role_id', 1)->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new InactivePostNotification($post, $user));
        }
        return response()->json(['message' => 'Super Admin notified successfully']);
    }

    public function deletePostRequest($postId)
    {
        $user = Auth::user();
        $post = Post::findOrFail($postId);

        $superAdmins = User::where('role_id', 1)->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new PostDeletionRequestNotification($post, $user));
        }

        return response()->json(['message' => 'Super Admin notified successfully']);
    }
}
