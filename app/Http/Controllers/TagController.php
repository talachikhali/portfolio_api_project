<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json(['message' => 'these are all the tags' , $tags] , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string'
        ]);
        $tag = Tag::create($validate);
        return response()->json(['message' => 'tag created successfully' , $tag], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tag = Tag::where('id' , $id)->with('projects')->first();
        if(!$tag){
            return response()->json(['message' => 'tag not found']);
        }

        return response()->json(['message' => 'this is the tag info' , $tag], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $tag = Tag::where('id' , $id)->first();

        if(!$tag){
            return response()->json(['message' => 'tag not found']);
        }

        $validate = $request->validate([
            'name' => 'required|string'
        ]);
        $tag->update($validate);
        return response()->json(['message' => 'tag updated successfully' , $tag], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tag = Tag::where('id' , $id)->first();
        if(!$tag){
            return response()->json(['message' => 'tag not found'], 404);
        }
        
        $tag->delete();
        return response()->json(['message' => 'tag deleted successfully' , $tag], 200);
    }
}
