<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class ApiHelper
{
    public static function makeRetryCallback(int $baseMs, string $aspect): callable
    {
        return function (int $attempt, Exception $exception) use ($baseMs, $aspect) {
            $wait = $baseMs * (int) pow(2, $attempt);

            Log::warning($exception, ['next_attempt_in_seconds' => $wait, 'aspect' => $aspect]);

            return $wait;
        };
    }

}
