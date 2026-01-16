<?php

namespace App\Http\Controllers;

use App\Models\Sign;
use App\Models\SignCategory;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SignsController extends Controller
{
    public function index(): Response
    {
        $categories = SignCategory::with(['notes', 'signs' => fn ($q) => $q->orderBy('position')])
            ->orderBy('group_number')
            ->get();

        $totalSigns = Sign::count();

        return Inertia::render('signs/index', [
            'categories' => $categories,
            'totalSigns' => $totalSigns,
        ]);
    }

    public function show(Sign $sign): JsonResponse
    {
        $sign->load('signCategory');

        $relatedQuestionsCount = $sign->questions()->count();

        return response()->json([
            'sign' => $sign,
            'related_questions_count' => $relatedQuestionsCount,
        ]);
    }
}
