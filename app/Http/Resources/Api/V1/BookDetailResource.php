<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'author'       => $this->author,
            'isbn'         => $this->isbn,
            'published_at' => $this->published_at,
            'genres'       => GenreResource::collection($this->genres),
            'reviews'      => ReviewResource::collection($this->reviews),
        ];
    }
}
