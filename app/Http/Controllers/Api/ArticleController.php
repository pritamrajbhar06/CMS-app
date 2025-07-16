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
        $filters = [
            'category' => request('category_id'),
            'status' => request('status'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
        ];

        $articles = $this->articleService->getAllArticles(auth()->user(), $filters);

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found'], 404);
        }

        return ArticleResource::collection($articles);
    }

    public function store(Request $request)
    {
        if (!$request->all()) {
            return response()->json(['message' => 'Request body is empty'], 400);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'status' => 'required|in:Draft,Published,Archived',
            'published_at' => 'required|date',
        ]);

        $article = $this->articleService->create([
            'title' => $request['title'],
            'content' => $request['content'],
            'author_id' => auth()->id(),
            'status' => ArticleStatus::value($request['status']),
            'published_at' => \Carbon\Carbon::parse($request['published_at'])->format('Y-m-d'),
        ]);

        $article->categories()->attach($request['categories']);

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
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'status' => 'sometimes|required|in:Draft,Published,Archived',
            'published_at' => 'sometimes|required|date',
        ]);

        $article = $this->articleService->getArticleById($id, auth()->user());

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        if($request->has('status')) {
            $validatedData['status'] = ArticleStatus::value($validatedData['status']);
        }

        $updatedArticle = $this->articleService->update($article->id, $validatedData);

        // Update categories if provided
        if ($request->has('categories')) {
            $updatedArticle->categories()->sync($request['categories']);
        }

        // Check if title or content has changed
        $titleChanged   = isset($validatedData['title']) && $validatedData['title'] !== $article->title;
        $contentChanged = isset($validatedData['content']) && $validatedData['content'] !== $article->content;

        // Dispatch jobs only if title or content has changed
        if ($titleChanged && $contentChanged) {
            GenerateSlug::dispatch($updatedArticle->id);
        }

        // Dispatch summary generation job if only content has changed
        if ($contentChanged) {
            GenerateSummary::dispatch($updatedArticle->id);
        }


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
