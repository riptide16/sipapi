<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Http\Resources\ErrorResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return new ErrorResource($e, 404, 'ERR4004');
        });

        $this->renderable(function (ValidationException $e, $request) {
            return new ErrorResource($e->errors(), 422, 'ERR4022');
        });

        $this->renderable(function (AuthenticationException $e) {
            return new ErrorResource($e->getMessage(), 401, 'ERR4001');
        });

        $this->renderable(function (OAuthServerException $e, $request) {
            return $this->renderOAuthException($e);
        });

        $this->renderable(function (QueryException $e) {
            // Constraint violation error
            if ($e->errorInfo[0] === '23000' && $e->errorInfo[1] === 1451) {
                return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
            }
        });

        $this->renderable(function (\Exception $e) {
            return new ErrorResource($e);
        });
    }

    protected function renderOAuthException(OAuthServerException $e)
    {
        switch ($e->getErrorType()) {
        case 'invalid_grant':
            switch ($e->getCode()) {
            case 6: // Invalid credentials
                return new ErrorResource(
                    __('auth.failed'),
                    400,
                    'ERR4100',
                );
            }
        default:
            return new ErrorResource($e, $e->getHttpStatusCode());
        }
    }
}
