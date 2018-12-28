<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return void
     * @throws \Exception
     */
    public function report(Exception $e)
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->server->get("SERVER_NAME") == "api.vatusa.net") {
            if ($e instanceOf NotFoundHttpException) {
                return response()->json(["status" => "error", "msg" => "Request not known"]);
            } else {
                return response()->json(["status" => "error", "msg" => "Unknown error"]);
            }
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return redirect('/405');
        }

        if ($this->isHttpException($e)) {
            return $this->renderHttpException($e);
        } else {
            // Convert all non-http exceptions to a proper 500 http exception
            // if we don't do this exceptions are shown as a default template
            // instead of our own view in resources/views/errors/500.blade.php
            if ($this->shouldReport($e) && !config('app.debug')) {
                $e = new HttpException(500, 'Whoops! An error occurred.');
            }

            return parent::render($request, $e);
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request                 $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
