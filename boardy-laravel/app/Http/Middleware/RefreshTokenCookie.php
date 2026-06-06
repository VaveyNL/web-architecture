<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RefreshTokenCookie
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('oauth/token') && $response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            if (is_array($data) && isset($data['refresh_token'])) {
                $refresh = $data['refresh_token'];
                unset($data['refresh_token']);
                $response->setContent(json_encode($data));
                $response->headers->setCookie(cookie(
                    'refresh_token', $refresh,
                    60 * 24 * 30,   // 30 дней (в минутах)
                    '/', null,
                    true,           // Secure (работает на localhost в Chrome)
                    true,           // HttpOnly
                    false,
                    'Lax'           // SameSite
                ));
            }
        }
        return $response;
    }
}
