<?php

namespace App\Jobs;

use App\Services\ArticleService;
use App\Services\OpenAiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSlug implements ShouldQueue
{
    use Queueable;

    protected $articleId;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $articleService = new ArticleService();
        $article = $articleService->getArticleById($this->articleId);

        $messages = 
            ['role' => 'user', 'content' => ' generate a unique slug based on the title and content of the article: ' . $article->title . ' ' . $article->content];

        $result = (new OpenAiService())->generateText($messages);

        if(empty($result)) {
            // Handle the case where no slug was generated
            return;
        }

        $exists = $articleService->checkIfArticleSlugExists($result);
        if($exists) {
            $result = $result . '-' . uniqid();
        }
        
        $article->update(['slug' => $result]);
    }
}
