<?php

namespace App\Http\Resources;

class NotificationResource extends Resource
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
            'content' => $this->data,
            'read_at' => $this->read_at,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
