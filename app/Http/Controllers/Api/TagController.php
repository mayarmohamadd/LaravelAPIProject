<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags|max:255',]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tag = Tag::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'data' => $tag,
            'message' => 'Tag created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = Tag::find($id);
        if(!$tag){
            return response()->json(['message'=>'Tag not found'],404);
        }
        return response()->json($tag, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = Tag::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:tags,name,' . $tag->id . '|max:255',]);
        $tag->update([
            'name' => $request->name,
        ]);
        return response()->json([
            'data' => $tag,
            'message' => 'Tag updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->json([
            'message' => 'Tag deleted successfully'
        ], 204);
    }
}
