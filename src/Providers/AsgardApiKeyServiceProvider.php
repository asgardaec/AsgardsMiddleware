<?php

namespace asgardaec\AsgardsMiddleware\Providers;

use asgardaec\AsgardsMiddleware\Console\Commands\ActivateApiKey;
use asgardaec\AsgardsMiddleware\Console\Commands\DeactivateApiKey;
use asgardaec\AsgardsMiddleware\Console\Commands\DeleteApiKey;
use asgardaec\AsgardsMiddleware\Console\Commands\GenerateApiKey;
use asgardaec\AsgardsMiddleware\Console\Commands\ListApiKeys;
use asgardaec\AsgardsMiddleware\Http\Middleware\AuthorizeAsgardApiKey;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AsgardApiKeyServiceProvider extends ServiceProvider
{
    const AUTH_HEADER = 'Econ-Secret-Key';

    /**
     * Handle the incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header(self::AUTH_HEADER);
        $apiKey = ApiKey::getByKey($header);

        if ($apiKey instanceof ApiKey) {
            $this->logAccessEvent($request, $apiKey);
            return $next($request);
        }

        return response([
            'errors' => [[
                'message' => 'Unauthorized'
            ]]
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
        $event->url        = $request->fullUrl();
        $event->save();
    }
    
}
