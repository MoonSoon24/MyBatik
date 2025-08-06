<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('designs', 'public');

        $design = new Design([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'image_path' => $path,
            'is_public' => true,
        ]);
        $design->save();

        return redirect()->route('gallery.index')->with('success', 'Your design has been shared successfully!');
    }

    public function toggleLike(Design $design)
    {
        $user = Auth::user();
        $like = $design->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $design->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $design->likes()->count(),
        ]);
    }

    public function addComment(Request $request, Design $design)
    {
        $request->validate(['body' => 'required|string']);

        $comment = $design->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        
        $comment->load('user');

        return response()->json(['comment' => $comment]);
    }

    public function update(Request $request, Design $design)
    {
        if (Auth::id() !== $design->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $design->title = $request->title;
        $design->save();

        return response()->json(['title' => $design->title]);
    }

    public function destroy(Design $design)
    {
        if (Auth::id() !== $design->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($design->image_path);

        $design->delete();

        return redirect()->route('gallery.index')->with('success', 'Your design has been deleted successfully!');
    }
}
