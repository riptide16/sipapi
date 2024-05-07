<?php

namespace App\Http\Resources;

class BannerResource extends Resource
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
            'name' => $this->name,
            'image' => $this->image ? config('services.frontend.storage_url').$this->image.'?stream' : null,
            'order' => $this->order,
            'url' => $this->url,
            'is_active' => $this->when($auth, $this->is_active),
        ];
    }
}
