<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /** Connection used by public course/subject endpoints. */
    private const PUBLIC_DATA_CONNECTION = 'ods';

    /**
     * Application health check
     *
     * Lightweight uptime probe. Returns 200 when the application is serving
     * requests and the public data database answers, 503 otherwise.
     *
     * @response array{status: string}
     *
     * @return JsonResponse
     **/
    public function getHealth(): JsonResponse
    {
        if (!$this->canQueryPublicDataDatabase()) {
            return response()->json(
                ['status' => 'error'],
                IlluminateResponse::HTTP_SERVICE_UNAVAILABLE,
                ['Cache-Control' => 'no-store']
            );
        }

        return response()->json(
            ['status' => 'ok'],
            IlluminateResponse::HTTP_OK,
            ['Cache-Control' => 'no-store']
        );
    }

    /**
     * Cheapest query that proves the public data connection is usable.
     **/
    private function canQueryPublicDataDatabase(): bool
    {
        try {
            DB::connection(self::PUBLIC_DATA_CONNECTION)->select('select 1');

            return true;
        } catch (\Exception $exception) {
            // Body stays generic for public callers; log the cause for operators.
            Log::warning('Health check database probe failed', [
                'connection' => self::PUBLIC_DATA_CONNECTION,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
