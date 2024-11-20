<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AnimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user_id = Auth::id();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'genre' => $this->genre,
            'rating' => $this->rating,
            'studio' => $this->studio,
            'status' => $this->status,
            'type' => $this->type,
            'episodes' => $this->episodes,
            'duration' => $this->duration,
            'synopsis' => $this->synopsis,
            'isLiked' => $user_id ? $this->favorites()->where('user_id', $user_id)->exists() : false,
        ];
    }
}
