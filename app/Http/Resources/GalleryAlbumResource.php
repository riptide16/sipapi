<?php

namespace App\Http\Resources;

class GalleryAlbumResource extends Resource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'galleries' => new GalleryCollection($this->whenLoaded('galleries')),
        ];
    }
}
