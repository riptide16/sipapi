<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\Resource;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseController;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

class AccessTokenController extends BaseController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        $response = $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        $response = json_decode((string) $response->getBody(), true);

        return new Resource($response);
    }
}
