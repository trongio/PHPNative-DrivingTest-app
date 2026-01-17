<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestHistoryController extends Controller
{
    /**
     * Display list of completed tests.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = TestResult::forUser($user->id)
            ->completed()
            ->with('licenseType')
            ->latest('finished_at');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'passed') {
                $query->passed();
            } elseif ($request->status === 'failed') {
                $query->failed();
            }
        }

        // Filter by test type
        if ($request->has('test_type')) {
            $query->where('test_type', $request->test_type);
        }

        $testResults = $query->paginate(20)->withQueryString();

        // Calculate stats
        $stats = [
            'total' => TestResult::forUser($user->id)->completed()->count(),
            'passed' => TestResult::forUser($user->id)->passed()->count(),
            'failed' => TestResult::forUser($user->id)->failed()->count(),
        ];

        return Inertia::render('test/history/index', [
            'testResults' => $testResults,
            'stats' => $stats,
            'filters' => [
                'status' => $request->input('status'),
                'test_type' => $request->input('test_type'),
            ],
        ]);
    }

    /**
     * Display detailed view of a completed test.
     */
    public function show(Request $request, TestResult $testResult): Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($testResult->user_id !== $user->id) {
            abort(403);
        }

        // Redirect to results if still in progress
        if (! $testResult->isCompleted()) {
            return redirect()->route('test.show', $testResult);
        }

        return Inertia::render('test/history/show', [
            'testResult' => [
                'id' => $testResult->id,
                'test_type' => $testResult->test_type,
                'status' => $testResult->status,
                'configuration' => $testResult->configuration,
                'questions' => $testResult->questions_with_answers,
                'answers_given' => $testResult->answers_given ?? [],
                'correct_count' => $testResult->correct_count,
                'wrong_count' => $testResult->wrong_count,
                'total_questions' => $testResult->total_questions,
                'score_percentage' => (float) $testResult->score_percentage,
                'time_taken_seconds' => $testResult->time_taken_seconds,
                'allowed_wrong' => $testResult->getAllowedWrong(),
                'started_at' => $testResult->started_at->toISOString(),
                'finished_at' => $testResult->finished_at?->toISOString(),
                'license_type_id' => $testResult->license_type_id,
                'license_type' => $testResult->licenseType?->only(['id', 'code', 'name']),
            ],
        ]);
    }

    /**
     * Delete a test result from history.
     */
    public function destroy(Request $request, TestResult $testResult): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($testResult->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $testResult->delete();

        return response()->json(['success' => true]);
    }
}
