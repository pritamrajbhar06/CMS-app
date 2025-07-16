<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ArticleService
{
    public function create(array $data)
    {
        return Article::create($data);
    }

    public function getAllArticles($user, array $filters= []): Collection
    {
        $query = Article::with('categories', 'author');

        if ($user->user_type === 'author') {
            $query->where('author_id', $user->id);
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', ArticleStatus::value($filters['status']));
        }

        if (!empty($filters['start_date'])) {
            $query->where('published_at', '>=', Carbon::parse($filters['start_date'])->format('Y-m-d'));
        }

        if (!empty($filters['end_date'])) {
            $query->where('published_at', '<=', Carbon::parse($filters['end_date'])->format('Y-m-d'));
        }

        return $query->latest()->get();
    }

    public function update($id, array $data)
    {
        $article = Article::where('id', $id)->update($data);
        return $article;
    }

    public function getArticleById(int $id, $user= null): ?Article
    {
        $query = Article::with('categories', 'author');

        if ($user && $user->user_type === 'author') {
            $query->where('author_id', $user->id);
        }

        return $query->find($id);
    }

    public function checkIfArticleSlugExists(string $slug): bool
    {
        return Article::where('slug', $slug)->exists();
    }
}