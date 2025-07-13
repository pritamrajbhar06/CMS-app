<?php

namespace App\Jobs;

use App\Services\ArticleService;
use App\Services\OpenAiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateSummary implements ShouldQueue
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
            ['role' => 'user', 'content' => 'generate a brief summary (2-3 sentences) of the article content. Here is the content: ' . $article->content];

        $result = (new OpenAiService())->generateText($messages);

        if(empty($result)) {
            Log::error("message: 'No summary generated for article ID: {$this->articleId}'");
            return;
        }

        $article->update(['summary' => $result]);
    }
}
