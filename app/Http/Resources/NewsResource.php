<?php

namespace App\Http\Resources;

class NewsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auth = auth()->check();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image ? config('services.frontend.storage_url').$this->image.'?stream' : null,
            'body' => $this->body,
            'published_date' => $this->published_date,
            'author' => new UserResource($this->whenLoaded('author')),
        ];
    }
}
