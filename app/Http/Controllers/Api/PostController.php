<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('user_id', Auth::id())->where('deleted_at', null)->orderBy('pinned', 'desc')->get();
        return response()->json($posts, 200);
    }

    public function show($id){
        $post = Post::where('user_id', Auth::id())->find($id);
        if(!$post){
            return response()->json(['message'=>'Post not found Or not login'],404);
        }
        return response()->json($post, 200);}

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'array','tags.*' => 'exists:tags,id']);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $coverImagePath = $request->file('cover_image')->store('cover_images', 'public');
        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'cover_image' => $coverImagePath,
            'pinned' => $request->pinned,]);
        $post->tags()->attach($request->tags);
        return response()->json($post, 201);}

    public function destroy($id) {
        $post = Post::where('user_id', Auth::id())->find($id);
        if(!$post){
            return response()->json(['message'=>'Post not found Or not login'],404);
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', Auth::id())->find($id);
        if(!$post){
            return response()->json(['message'=>'Post not found Or not login'],404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'sometimes|image',
            'pinned' => 'required|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id']);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);}
        if ($request->hasFile('cover_image')) {
            Storage::disk('public')->delete($post->cover_image);
            $coverImagePath = $request->file('cover_image')->store('cover_images', 'public');
            $post->cover_image = $coverImagePath;}
        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'pinned' => $request->pinned,
        ]);
        $post->tags()->sync($request->tags);
        return response()->json($post, 200);
    }

    public function trashed(){
        $posts = Post::onlyTrashed()->where('user_id', Auth::id())->get();
        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No trashed posts found'], 404);
        }
        return response()->json($posts, 200);}


    public function restore($id){
        $post = Post::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $post->restore();
        return response()->json(['message' => 'Post restored successfully'], 200);}


}
