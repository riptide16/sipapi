<?php

namespace App\Http\Resources;

class PublicMenuResource extends Resource
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
            'url' => $this->url,
            'order' => $this->order,
            'is_default' => $this->is_default,
            'page_id' => $this->page_id,
            'page' => new PageResource($this->whenLoaded('page')),
            'parent' => new static($this->whenLoaded('parent')),
            'children' => new PublicMenuCollection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
