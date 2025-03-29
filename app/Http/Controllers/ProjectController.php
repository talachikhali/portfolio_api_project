<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $projects = Project::where('user_id', $user->id)->with( 'user' ,'tags' , 'skills')->get();
        return response()->json(['message' => 'these are all the projects by this user', $projects], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image',
            'project_link' => 'required|url'
        ]);

        $imageName = $request->file('image')->store('images/projects', 'public');

        $project = Project::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => Purifier::clean($validate['description']),
            'image' => $imageName,
            'project_link' => $request->project_link
        ]);

        $tagIds = explode(',', $request->input('tags'));
        $skillIds = explode(',', $request->input('skills'));

        $project->tags()->attach($tagIds);
        $project->save();

        $project->skills()->attach($skillIds);
        $project->save();

        return response()->json(['message' => 'project has been created', 'project' => $project], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user , $id)
    {
        $project = Project::where('id' , $id)->with( 'tags', 'skills')->first();

        if(!$project){
            return response()->json(['message' => 'project not found'], 404);
        }

        return response()->json(['message' => 'this is the required project' , $project], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $project = Project::where('id' , $id)->with( 'tags', 'skills')->first();
        
        if(!$project){
            return response()->json(['message' => 'project not found'], 404);
        }
        
        if($project->user_id != auth()->id()){
            return response()->json(['message' => 'can not delete a project that is not yours' ] , 403);
        }

        $validate = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'image' => 'image',
            'project_link' => 'url'
        ]);

        if($request->hasFile('image')){
            $imageName = $request->file('image')->store('images/projects', 'public');
            if (Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
        }
        else{
            $imageName = $project->image;
        }

        $title = $request->title ? $request->title: $project->title;
        $description =  $request->description ?  Purifier::clean($validate['description']): $project->description;
        $project_link = $request->project_link ? $request->project_link: $project->project_link;


        $project->update([
            'title' => $title,
            'description' => $description,
            'image' => $imageName,
            'project_link' => $project_link
        ]);

        $tagIds = explode(',', $request->input('tags'));
        $project->tags()->sync($tagIds);
        $skillIds = explode(',', $request->input('skills'));
        $project->tags()->sync($skillIds);

        return response()->json(['message' => 'project updated successfully' , $project] , 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::where('id' , $id)->first();

        if(!$project){
            return response()->json(['message' => 'project not found'], 404);
        }
        if($project->user_id != auth()->id()){
            return response()->json(['message' => 'can not delete a project that is not yours' ] , 403);
        }

        if(Storage::disk('public')->exists($project->image)) {
            Storage::disk('public')->delete($project->image);
        }

        $project->tags()->detach();
        $project->skills()->detach();

        $project->delete();
        return response()->json(['message' => 'project deleted succefully' ],200);
    }
}
