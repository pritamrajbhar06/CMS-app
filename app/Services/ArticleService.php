<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
    public function create(array $data)
    {
        return Article::create($data);
    }

    public function getAllArticles($user, array $filters= []): Collection
    {
        $query = Article::with('categories', 'author');

        if ($user->role === 'author') {
            $query->where('author_id', $user->id);
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories_id', $filters['category']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', ArticleStatus::value($filters['status']));
        }

        if (!empty($filters['start_date'])) {
            $query->where('published_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('published_at', '<=', $filters['end_date']);
        }

        return $query->latest()->get();
    }

    public function update(Article $article, array $data)
    {
        $article->update($data);
        return $article;
    }

    public function getArticleById(int $id, $user= null): ?Article
    {
        $query = Article::with('categories', 'author');

        if ($user && $user->role === 'author') {
            $query->where('user_id', $user->id);
        }

        return $query->find($id);
    }

    public function checkIfArticleSlugExists(string $slug): bool
    {
        return Article::where('slug', $slug)->exists();
    }
}