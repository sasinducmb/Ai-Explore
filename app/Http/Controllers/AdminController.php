<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromptingAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get design results (using raw table since we might not have a model)
        $designResults = collect();
        try {
            // Check if table exists
            if (DB::getSchemaBuilder()->hasTable('design_answers')) {
                $designData = DB::table('design_answers')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Manually paginate the collection
                $currentPage = Paginator::resolveCurrentPage('design_page');
                $perPage = 10;
                $currentPageItems = $designData->slice(($currentPage - 1) * $perPage, $perPage)->all();

                $designResults = new LengthAwarePaginator(
                    $currentPageItems,
                    $designData->count(),
                    $perPage,
                    $currentPage,
                    [
                        'path' => request()->url(),
                        'pageName' => 'design_page',
                    ]
                );
            } else {
                // Create empty paginator if table doesn't exist
                $designResults = new LengthAwarePaginator(
                    [],
                    0,
                    10,
                    1,
                    [
                        'path' => request()->url(),
                        'pageName' => 'design_page',
                    ]
                );
            }
        } catch (\Exception $e) {
            // Create empty paginator on error
            $designResults = new LengthAwarePaginator(
                [],
                0,
                10,
                1,
                [
                    'path' => request()->url(),
                    'pageName' => 'design_page',
                ]
            );
            \Log::error('Error fetching design results: ' . $e->getMessage());
        }

        // Get prompting results
        try {
            $promptingResults = PromptingAnswer::orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'prompting_page');
        } catch (\Exception $e) {
            // Create empty paginator on error
            $promptingResults = new LengthAwarePaginator(
                [],
                0,
                10,
                1,
                [
                    'path' => request()->url(),
                    'pageName' => 'prompting_page',
                ]
            );
            \Log::error('Error fetching prompting results: ' . $e->getMessage());
        }

        return view('admin.admin', compact('designResults', 'promptingResults'));
    }

    public function showDesignResult($id)
    {
        try {
            $result = DB::table('design_answers')->where('id', $id)->first();

            if (!$result) {
                return response('<div class="text-center py-4 text-red-600">Result not found</div>', 404);
            }

            return view('admin.partials.design-detail', compact('result'));
        } catch (\Exception $e) {
            \Log::error('Error loading design result: ' . $e->getMessage());
            return response('<div class="text-center py-4 text-red-600">Error loading design result</div>', 500);
        }
    }

    public function showPromptingResult($id)
    {
        try {
            $result = PromptingAnswer::findOrFail($id);
            return view('admin.partials.prompting-detail', compact('result'));
        } catch (\Exception $e) {
            \Log::error('Error loading prompting result: ' . $e->getMessage());
            return response('<div class="text-center py-4 text-red-600">Error loading prompting result</div>', 500);
        }
    }
}
