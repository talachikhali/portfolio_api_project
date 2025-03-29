<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'projects' => $user->projects()->latest()->get(),
            'skills' => $user->skills,
            'services' => $user->services,
            'testimonials' => $user->testimonials,
            'messages' => $user->receivedMessages()->latest()->get(),
            'links' => $user->links,
        ]);
    }
}
