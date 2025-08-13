<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PromptingAnswer;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Get user's design results
        $designResults = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('design_answers')) {
                $designResults = DB::table('design_answers')
                    ->where('name', $user->name)
                    ->orWhere('session_id', session()->getId())
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching user design results: ' . $e->getMessage());
        }

        // Get user's prompting results
        $promptingResults = collect();
        try {
            $promptingResults = PromptingAnswer::where('name', $user->name)
                ->orWhere('session_id', session()->getId())
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching user prompting results: ' . $e->getMessage());
        }

        return view('dashboards.parent', compact('designResults', 'promptingResults'));
    }

    public function showDesignResult($id)
    {
        $user = Auth::user();

        try {
            $result = DB::table('design_answers')
                ->where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('name', $user->name)
                          ->orWhere('session_id', session()->getId());
                })
                ->first();

            if (!$result) {
                return response('<div class="text-center py-4 text-red-600">Result not found or access denied</div>', 404);
            }

            return view('admin.partials.design-detail', compact('result'));
        } catch (\Exception $e) {
            \Log::error('Error loading user design result: ' . $e->getMessage());
            return response('<div class="text-center py-4 text-red-600">Error loading result</div>', 500);
        }
    }

    public function showPromptingResult($id)
    {
        $user = Auth::user();

        try {
            $result = PromptingAnswer::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('name', $user->name)
                          ->orWhere('session_id', session()->getId());
                })
                ->firstOrFail();

            return view('admin.partials.prompting-detail', compact('result'));
        } catch (\Exception $e) {
            \Log::error('Error loading user prompting result: ' . $e->getMessage());
            return response('<div class="text-center py-4 text-red-600">Error loading result</div>', 500);
        }
    }
}
