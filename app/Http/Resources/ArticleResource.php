<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Enums\ArticleStatus;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status ? ArticleStatus::label($this->status) : null,
            'author' => $this->author->name,
            'categories' => $this->categories->pluck('name'),
            'published_at' => $this->published_at ? Carbon::parse($this->published_at)->format('d-m-Y') : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y') : null,
        ];
    }
}
