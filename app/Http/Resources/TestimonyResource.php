<?php

namespace App\Http\Resources;

class TestimonyResource extends Resource
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
            'content' => $this->content,
            'photo' => $this->photo ? config('services.frontend.storage_url').$this->photo.'?stream' : null,
        ];
    }
}
