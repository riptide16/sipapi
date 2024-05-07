<?php

namespace App\Http\Resources;

class InstrumentResource extends Resource
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
            'category' => $this->category,
            'aspects' => new InstrumentAspectCollection($this->whenLoaded('aspects')),
            'components' => new InstrumentComponentCollection($this->whenLoaded('components')),
        ];
    }
}
