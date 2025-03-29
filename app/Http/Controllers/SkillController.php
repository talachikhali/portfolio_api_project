<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $skills = Skill::where('user_id', $id)->get();

        return response()->json(['message' => 'these are the skill for the wanted user' , 'skills' => $skills], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'profficiency' => 'required|integer|min:1|max:100'
        ]);

        $skill = Skill::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'profficiency' => $request->profficiency
        ]);
        return response()->json(['message' => 'skill added successfully', 'skill' => $skill], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show( User $user, $id)
    {
        $skill = Skill::where('id' , $id)->with('projects')->first();
        if(!$skill){
            return response()->json(['message' => 'this is skill does not exist'], 404);
        }
        return response()->json(['message' => 'this is the required skill' , 'skill' => $skill], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $skill = Skill::where('id' , $id)->first();
        if(!$skill){
            return response()->json(['message' => 'this skill doesnt exist'] , 404);
        }

        if($skill->user_id != auth()->id()){
            return response()->json(['message' => 'can not update a skill that isnt in your portfolio'], 403);
        }

        $validate = $request->validate([
            'name' => 'string',
            'profficiency' => 'integer|min:1|max:100'
        ]);

        $name = $request->name ? $request->name : $skill->name;
        $pro = $request->profficiency ? $request->profficiency: $skill->profficiency;
        
        $skill->update([
            'name' => $name,
            'profficiency' => $pro
        ]);

        return response()->json(['message' => 'skill updated successfully' , 'skill' => $skill] , 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $skill = Skill::where('id' , $id)->first();

        if(!$skill){
            return response()->json(['message' => 'skill doesnt exist'], 404);
        }
        if($skill->user_id != auth()->id()){
            return response()->json(['message' => 'can not delete a skill that isnt in your portfolio'], 403);
        }

        $skill->delete();
        return response()->json(['message' => 'skill deleted successfully'] , 200);
    }
}
