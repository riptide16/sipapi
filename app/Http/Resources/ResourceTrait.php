<?php

namespace App\Http\Resources;

trait ResourceTrait
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => $this->determineSuccess(),
            'message' => $this->message,
        ];
    }

    /**
     * Determine the value of this resource's success response.
     *
     * @return bool
     */
    protected function determineSuccess()
    {
        switch (substr((string) $this->statusCode, 0, 1)) {
        case '1':
        case '2':
        case '3':
            return true;
        case '4':
        case '5':
        default:
            return false;
        }
    }
}

