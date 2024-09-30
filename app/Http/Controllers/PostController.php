<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use App\Notifications\InactivePostNotification;
use App\Notifications\PostDeletionNotification;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Psy\debug;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $postsQuery = Post::with('user.department');
        if ($user->role_id == 1) {
            $posts = $postsQuery->paginate(9);
        } elseif ($user->role_id == 2) {
            $posts = $postsQuery->where(function ($query) use ($user) {
                $query->where('status', Post::STATUS_PUBLIC)
                        ->orWhere('user_id', auth()->id())
                    ->orWhere(function ($query) use ($user) {
                        $query->whereIn('status', [Post::STATUS_PRIVATE, Post::STATUS_VISIBLE])
                            ->whereHas('user', function ($query) use ($user) {
                                $query->where('department_id', $user->department_id);
                            });
                    });
            })->paginate(9);
        } else {
            $posts = $postsQuery->where(function ($query) use ($user) {
                $query->where('status', Post::STATUS_PUBLIC)
                ->orWhere('user_id', auth()->id())
                    ->orWhere(function ($query) use ($user) {
                        $query->where('status', Post::STATUS_VISIBLE)
                            ->whereHas('user', function ($query) use ($user) {
                                $query->where('department_id', $user->department_id);
                            });
                    });
            })->paginate(9);
        }

        return response()->json($posts);
    }



    public function getUnverifiedPosts()
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $posts = Post::where(function ($query) {
            $query->where('status', Post::STATUS_INACTIVE)
                ->orWhere('status', Post::STATUS_PRIVATE);
        })
            ->whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->with('user')
            ->paginate(7);

        return response()->json($posts);
    }


    public function getPersonalPosts()
    {
        $user = Auth::user();
        $posts = Post::where('user_id', $user->id)->with('user')->paginate(7); 
        return response()->json($posts);
    }


    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('files', 'public');
            $post->file = $filePath;
        }
        $post->save();
        return response()->json(['message' => 'Post updated successfully']);
    }

    public function updatePostStatus(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->status = $request->status;
        $post->save();
        return response()->json(['message' => 'Status updated successfully']);
    }
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        if ($post->user->role_id != 1) {
            $superAdmins = User::where('role_id', 1)->get();
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->notify(new PostDeletionNotification($post, $post->user));
            }
        }
        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function store(PostRequest $request)
    {
        $filePath = $request->file('file')->store('posts', 'public');

        $post = Post::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'file' => $filePath,
            'status' => $request->input('status', 0),
            'user_id' => Auth::id(),
        ]);
        return response()->json($post, 201);
    }
}
