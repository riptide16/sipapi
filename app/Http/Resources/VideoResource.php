<?php

namespace App\Http\Resources;

class VideoResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'youtube_id' => $this->youtube_id,
            'description' => $this->description,
            'youtube_url' => $this->youtubeUrl(),
            'is_homepage' => $this->is_homepage
        ];
    }
}
