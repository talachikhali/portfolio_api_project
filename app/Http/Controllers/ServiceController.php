<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index($id)
    {
        $services = Service::where('user_id' , $id)->get();
        return response()->json(['message' => 'these are all the services this user offers' , 'services' => $services], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable'
        ]);

        if($request->hasFile('icon')){
            $iconName = $request->file('icon')->store('icons/services', 'public');
        }
        else{
            $iconName = null;
        }

        $service = Service::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' =>  Purifier::clean($validate['description']),
            'icon' => $iconName
        ]);

        return response()->json(['message' => 'service created successfully' , 'service' => $service], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user ,$id)
    {
        $service = Service::where('id' , $id)->first();
        if(!$service){
            return response()->json(['message' => 'service not found'], 404);
        }
        return response()->json(['message' => 'this is the required service ' , 'service' => $service], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $service = Service::where('id' , $id)->first();
        if(!$service){
            return response()->json(['message' => 'service not found'], 404);
        }
        if($service->user_id != auth()->id()){
            return response()->json(['message' => 'you cannot update a service that isnt yours' ] , 403);
        }
        if($request->icon){
            $iconName = $request->icon;
            if ($service->icon && Storage::disk('public')->exists($service->icon)) {
                Storage::disk('public')->delete($service->icon);
            }
        }
        else{
            $iconName = $service->icon;
        }
        $validate = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'icon' => 'nullable'
        ]);

        $title = $request->title ? $request->title: $service->title;
        $description = $request->description ?  Purifier::clean($validate['description']): $service->description;

        $service->update([
            'user_id' => auth()->id(),
            'title' => $title,
            'description' => $description,
            'icon' => $iconName
        ]);

        return response()->json(['message' => 'service updated successfully' , 'service' => $service], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Service::where('id' , $id)->first();
        if(!$service){
            return response()->json(['message' => 'service not found'], 404);
        }
        if($service->user_id != auth()->id()){
            return response()->json(['message' => 'you cannot update a service that isnt yours' ] , 403);
        }
        if ($service->icon && Storage::disk('public')->exists($service->icon)) {
            Storage::disk('public')->delete($service->icon);
        }
        $service->delete();
        return response()->json(['message' => 'service deleted succefully' ],200);
    }
}
