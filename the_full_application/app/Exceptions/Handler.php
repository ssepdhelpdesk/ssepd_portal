<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        Log::error('🚨 Unhandled Exception Occurred', [
            'message'  => $exception->getMessage(),
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'trace'    => $exception->getTraceAsString(),
            'url'      => request()->fullUrl(),
            'method'   => request()->method(),
            'ip'       => request()->ip(),
            'user_id'  => Auth::check() ? Auth::id() : null,
            'time'     => now()->toDateTimeString(),
        ]);

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
            $maxPostSize = ini_get('post_max_size') ?: '64M';
            $message = "The total upload size exceeds the server limit of {$maxPostSize}. Please reduce the number or file size of your uploaded photos and documents.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 413);
            }

            return redirect()->back()->with('error', $message);
        }

        return parent::render($request, $exception);
    }
}
