<?php

namespace App\Http\Controllers\Api;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSlug;
use App\Jobs\GenerateSummary;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {
        $articles = $this->articleService->getAllArticles(auth()->user());

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found'], 404);
        }

        return response()->json($articles);
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

    public function show($id)
    {
        $article = $this->articleService->getArticleById($id, auth()->user());

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return new ArticleResource($article);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'status' => 'required|in:Draft,Published,Archived',
            'published_at' => 'nullable|date',
        ]);

        $article = $this->articleService->getArticleById($id, auth()->user());

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $updatedArticle = $this->articleService->update($article, [
            'title' => $request['title'],
            'content' => $request['content'],
            'status' => ArticleStatus::value($request['status']),
            'published_at' => $request['published_at'] ?? null,
            'author_id' => auth()->user()->id,
        ]);

        $updatedArticle->categories()->sync($request['categories']);

        // Dispatch jobs
        GenerateSlug::dispatch($updatedArticle->id);
        GenerateSummary::dispatch($updatedArticle->id);

        return response()->json(['message' => 'Article updated. Slug and summary generation is in progress.']);
    }

    public function destroy($id)
    {
        $article = $this->articleService->getArticleById($id, auth()->user());

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

}
