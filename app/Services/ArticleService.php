<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
    public function create(array $data)
    {
        return Article::create($data);
    }

    public function getAllArticles($user): Collection
    {
        $query = Article::with('categories', 'author');

        if ($user->role === 'author') {
            $query->where('author_id', $user->id);
        }

        return $query->latest()->get();
    }

    public function update(Article $article, array $data)
    {
        $article->update($data);
        return $article;
    }

    public function getArticleById(int $id, $user): ?Article
    {
        $query = Article::with('categories', 'author');

        if ($user->role === 'author') {
            $query->where('author_id', $user->id);
        }

        return $query->find($id);
    }

    public function checkIfArticleSlugExists(string $slug): bool
    {
        return Article::where('slug', $slug)->exists();
    }
}