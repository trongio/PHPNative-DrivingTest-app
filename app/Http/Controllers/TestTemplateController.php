<?php

namespace App\Http\Controllers;

use App\Models\TestTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestTemplateController extends Controller
{
    /**
     * List user's templates (JSON response for dynamic loading).
     */
    public function index(Request $request): JsonResponse
    {
        $templates = TestTemplate::forUser($request->user()->id)
            ->with('licenseType')
            ->latest()
            ->get();

        return response()->json($templates);
    }

    /**
     * Store a new template.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_type_id' => 'nullable|exists:license_types,id',
            'question_count' => 'required|integer|min:5|max:100',
            'time_per_question' => 'required|integer|min:30|max:180',
            'failure_threshold' => 'required|integer|min:1|max:50',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:question_categories,id',
        ]);

        $template = TestTemplate::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'license_type_id' => $request->license_type_id,
            'question_count' => $request->question_count,
            'time_per_question' => $request->time_per_question,
            'failure_threshold' => $request->failure_threshold,
            'category_ids' => $request->category_ids ?? [],
            'excluded_question_ids' => [],
        ]);

        $template->load('licenseType');

        return response()->json($template, 201);
    }

    /**
     * Update an existing template.
     */
    public function update(Request $request, TestTemplate $testTemplate): JsonResponse
    {
        $this->authorize('update', $testTemplate);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'license_type_id' => 'nullable|exists:license_types,id',
            'question_count' => 'sometimes|required|integer|min:5|max:100',
            'time_per_question' => 'sometimes|required|integer|min:30|max:180',
            'failure_threshold' => 'sometimes|required|integer|min:1|max:50',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:question_categories,id',
        ]);

        $testTemplate->update([
            'name' => $request->name ?? $testTemplate->name,
            'license_type_id' => $request->license_type_id ?? $testTemplate->license_type_id,
            'question_count' => $request->question_count ?? $testTemplate->question_count,
            'time_per_question' => $request->time_per_question ?? $testTemplate->time_per_question,
            'failure_threshold' => $request->failure_threshold ?? $testTemplate->failure_threshold,
            'category_ids' => $request->has('category_ids') ? $request->category_ids : $testTemplate->category_ids,
        ]);

        $testTemplate->load('licenseType');

        return response()->json($testTemplate);
    }

    /**
     * Delete a template.
     */
    public function destroy(Request $request, TestTemplate $testTemplate): JsonResponse
    {
        $this->authorize('delete', $testTemplate);

        $testTemplate->delete();

        return response()->json(['success' => true]);
    }
}
