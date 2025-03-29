<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $testimonials = Testimonial::where('user_id', $id)->get();
        return response()->json(['message' => 'these are the testimonials for the user', 'testimonials' => $testimonials], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'feedback' => 'required|string'
        ]);

        $testimonial = Testimonial::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'role' => $request->role,
            'feedback' =>  Purifier::clean($validate['feedback'])
        ]);

        return response()->json(['message' => 'testimonial  created successfully', 'link'=> $testimonial], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user , $id)
    {
        $testimonial = Testimonial::where('id' , $id)->first();
        if(!$testimonial ){
            return response()->json(['message' => 'testimonial  not found'], 404);
        }
        return response()->json(['message' => 'this is the wanted testimonial' , 'testimonial' => $testimonial], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::where('id' , $id)->first();
        if(!$testimonial ){
            return response()->json(['message' => 'testimonial  not found'], 404);
        }
        if($testimonial->user_id != auth()->id()){
            return response()->json(['message' => 'can not edit a testimonial that isnt yours'], 403);
        }
        $validate = $request->validate([
            'name' => 'string',
            'role' => 'string',
            'feedback' => 'string'
        ]);

        $name =  $request->name ? $request->name : $testimonial->name; 
        $role =  $request->role ? $request->role : $testimonial->role; 
        $feedback =  $request->feedback ? Purifier::clean($validate['feedback']) : $testimonial->feedback; 


        $testimonial->update([
            'user_id' => auth()->id(),
            'name' => $name,
            'role' => $role,
            'feedback' => $feedback
        ]);

        return response()->json(['message' => 'testimonial updated succesfully', 'testimonial' => $testimonial], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::where('id' , $id)->first();
        if(!$testimonial ){
            return response()->json(['message' => 'testimonial  not found'], 404);
        }
        if($testimonial->user_id != auth()->id()){
            return response()->json(['message' => 'can not delete a testimonial that isnt yours'], 403);
        }

        $testimonial->delete();

        return response()->json(['message' => 'testimonial deleted succesfully'], 200);

    }
}
