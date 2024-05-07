<?php

namespace App\Http\Resources;

class MenuResource extends Resource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'order' => $this->order,
            'permissions' => new PermissionCollection($this->whenLoaded('permissions')),
            'icon' => $this->icon
        ];
    }
}
