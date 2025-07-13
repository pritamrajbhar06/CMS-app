<?php

namespace App\Http\Controllers\Api;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSlug;
use App\Jobs\GenerateSummary;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'status' => 'required|in:Draft,Published,Archived',
            'published_at' => 'nullable|date',
        ]);

        $article = $this->articleService->create([
            'title' => $request['title'],
            'content' => $request['content'],
            'author_id' => auth()->id(),
            'status' => ArticleStatus::value($request['status']),
            'published_at' => $request['published_at'] ?? null,
        ]);

        $article->categories()->attach($request['categories']);

        // Dispatch jobs
        GenerateSlug::dispatch($article->id);
        GenerateSummary::dispatch($article->id);

        return response()->json(['message' => 'Article created. Slug and summary generation is in progress.']);
    }

}
