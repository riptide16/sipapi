<?php

namespace App\Http\Resources;

class ActivityLogResource extends Resource
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
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject' => $this->resolveResource($this->subject_type, $this->subject),
            'causer_type' => $this->causer_type,
            'causer' => $this->resolveResource($this->causer_type, $this->causer),
            'changes' => $this->changes,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
        ];
    }
}
