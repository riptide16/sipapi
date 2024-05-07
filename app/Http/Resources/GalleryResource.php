<?php

namespace App\Http\Resources;

class GalleryResource extends Resource
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
            'caption' => $this->caption,
            'published_date' => $this->published_date,
            'image' => $this->image ? config('services.frontend.storage_url').$this->image.'?stream' : null,
            'album' => new GalleryAlbumResource($this->whenLoaded('album')),
            'is_homepage' => $this->is_homepage
        ];
    }
}
