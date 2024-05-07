<?php

namespace App\Http\Resources;

class AccreditationSimulationResource extends Resource
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
            'code' => $this->code,
            'status' => $this->status,
            'predicate' => $this->predicate,
            'created_at' => $this->created_at,
            'user_id' => $this->user_id,
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'contents' => new AccreditationContentCollection($this->whenLoaded('contents')),
            $this->mergeWhen(isset($this->resource->result), [
                'results' => $this->resource->results(),
            ]),
            $this->mergeWhen(isset($this->resource->result) && isset($this->resource->finalResult), [
                'finalResult' => $this->resource->finalResult(),
            ]),
        ];
    }
}
