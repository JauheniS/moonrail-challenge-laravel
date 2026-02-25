<?php

namespace App\Exceptions;

use App\Models\Player;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
        $this->renderable(function (NotFoundHttpException $e, $request) {
            $prev = $e->getPrevious();
            if ($prev instanceof ModelNotFoundException && $prev->getModel() === Player::class) {
                return response()->json(['message' => 'Player not found'], 404);
            }

            return null;
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($e->getModel() === Player::class) {
                return response()->json(['message' => 'Player not found'], 404);
            }

            return null;
        });
    }
}
