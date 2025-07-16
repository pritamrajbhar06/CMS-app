<?php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService
{    
    public function generateText(array $messages): string
    {
        $result = OpenAI::chat()->create([ 
            'model' => 'gpt-4o', 
            'messages' => $messages, 
            ]);
        
        return $result->choices[0]->message->content ?? '';
    }
}