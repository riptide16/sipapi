<?php

namespace App\Http\Resources;

class VillageResource extends Resource
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
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'subdistrict' => new SubdistrictResource($this->whenLoaded('subdistrict')),
        ];
    }
}
