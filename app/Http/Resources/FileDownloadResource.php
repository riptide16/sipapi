<?php

namespace App\Http\Resources;

class FileDownloadResource extends Resource
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
            'filename' => $this->filename,
            'is_published' => $this->is_published,
            'is_preset' => $this->is_preset,
            'attachment' => $this->attachment 
                ? config('services.frontend.storage_url').$this->attachment
                : null,
        ];
    }
}
