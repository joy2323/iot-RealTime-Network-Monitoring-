<?php

namespace App\Http\Middleware;

use App\ApiKey;
use App\ApiKeyAccessEvent;
use Closure;
use Illuminate\Http\Request;

class AuthorizeApiKey
{
    const AUTH_HEADER = 'X-Authorization';

    /**
     * Handle the incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {

        $clientKey = $request->header(self::AUTH_HEADER);

        if ($clientKey == null) {
            $clientKey = $request->access_key;
        }

        $apiKey = ApiKey::getByKey($clientKey);

        if ($apiKey instanceof ApiKey) {
            $this->logAccessEvent($request, $apiKey);
            return $next($request);
        }

        return response([
            'errors' => [[
                'message' => 'Unauthorized',
            ]],
        ], 401);
    }

    /**
     * Log an API key access event
     *
     * @param Request $request
     * @param ApiKey  $apiKey
     */
    protected function logAccessEvent(Request $request, ApiKey $apiKey)
    {
        $event = new ApiKeyAccessEvent;
        $event->api_key_id = $apiKey->id;
        $event->ip_address = $request->ip();
        $event->url = $request->fullUrl();
        $event->save();
    }
}