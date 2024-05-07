<?php

namespace App\Http\Resources;

class EvaluationResource extends Resource
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
            'document_file' => $this->document_file ? config('services.frontend.secure_storage_url').$this->document_file : null,
            'accreditation' => new AccreditationResource($this->whenLoaded('accreditation')),
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'assessor' => new UserResource($this->whenLoaded('assessor')),
            'contents' => new EvaluationContentCollection($this->whenLoaded('contents')),
            'recommendations' => $this->recommendations,
            'accreditation_id' => $this->accreditation_id,
            'created_at' => $this->created_at,
            $this->mergeWhen(isset($this->need_upload_document), [
                'need_upload_document' => $this->need_upload_document,
            ]),
            $this->mergeWhen(optional($this->contents)->isNotEmpty(), [
                'evaluation_result' => $this->evaluationResult(),
                'final_result' => $this->finalResult(),
            ]),
        ];
    }
}
