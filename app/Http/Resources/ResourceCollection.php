<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

class ResourceCollection extends BaseResourceCollection
{
    use ResourceTrait;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  int    $statusCode
     * @param  string $message
     * @return void
     */
    public function __construct($resource, $statusCode = 200, $message = null)
    {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
        $this->message = $message ?? __('info.success');
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
