<?php

namespace App\Services;

use App\Models\Article;

class ArticleService
{
    public function create(array $data)
    {
        return Article::create($data);
    }


    public function update(Article $article, array $data)
    {
        $article->update($data);
        return $article;
    }

    public function getArticleById(int $id): ?Article
    {
        return Article::find($id);
    }

    public function checkIfArticleSlugExists(string $slug): bool
    {
        return Article::where('slug', $slug)->exists();
    }
}