<?php

namespace App\Http\Controllers;

use App\Models\PromptingResult;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function dashboard()
    {
        $results = PromptingResult::orderBy('created_at', 'desc')->paginate(10);
        return view('dashboards.admin', compact('results'));
    }

    public function showResult($sessionId)
    {
        $result = PromptingResult::where('session_id', $sessionId)->firstOrFail();
        return view('admin.results.show', compact('result'));
    }


}
