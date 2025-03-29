<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use Storage;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $links = Link::where('user_id' , $id)->get();
        return response()->json(['message' => 'these are the contact links to the user' , 'links' => $links] , 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'platform' => 'required|string',
            'link' => 'required|url',
            'icon' => 'nullable' //idk if nullable is correct
        ]);

        if($request->hasFile('icon')){
            $iconName =  $request->file('icon')->store('icons/links', 'public');
        }
        else{
            $iconName = null;
        }

        $link = Link::create([
            'user_id' => auth()->id(),
            'platform' => $request->platform,
            'link' => $request->link,
            'icon' => $iconName
        ]);
        
        return response()->json(['message' => 'link created successfully' , 'link' => $link], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user ,$id)
    {
        $link = Link::where('id' , $id)->first();

        if(!$link){
            return response()->json(['message' => 'link not found'], 404);
        }
        return response()->json(['message' => 'this is the required link' , 'link' => $link] , 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $link = Link::where('id' , $id)->first();

        if(!$link){
            return response()->json(['message' => 'link not found'] , 404);
        }
        if($link->user_id != auth()->id()){
            return response()->json(['message' => 'can not edit a link that isnt yours'] , 403);
        }

        $validate = $request->validate([
            'platform' => 'string',
            'link' => 'url',
            'icon' => 'nullable' //idk if nullable is correct
        ]);

        if($request->hasFile('icon')){
            $iconName =  $request->file('icon')->store('icons/links', 'public');
            if($link->icon && Storage::disk('public')->exists($link->icon)){
                Storage::disk('public')->delete($link->icon);
            }
        }
        else{
            $iconName = $link->icon;
        }

        $link->update([
            'user_id' => auth()->id(),
            'platform' => $request->platform,
            'link' => $request->link,
            'icon' => $iconName
        ]);

        return response()->json(['message' => 'link updated successfully', 'link' => $link] , 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $link = Link::where('id' , $id)->first();

        if(!$link){
            return response()->json(['message' => 'link not found'], 404);
        }
        if($link->user_id != auth()->id()){
            return response()->json(['message' => 'can not delete a link that isnt yours'] , 403);
        }

        if($link->icon && Storage::disk('public')->exists($link->icon)){
            Storage::disk('public')->delete($link->icon);
        }

        $link->delete();
        return response()->json(['message' => 'link deleted successfully'] , 200);
    }
}
