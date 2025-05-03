<?php

namespace Itstudioat\Spa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Itstudioat\Spa\Services\RouteService;
use Itstudioat\Spa\Enums\RouteResult;
use Symfony\Component\HttpFoundation\Response;

class ApiAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $routeService = new RouteService();

        // z.B. '/admin/users/7'
        $fullPath = '/' . ltrim($request->path(), '/');

        // z.B. 'GET'
        $method = strtoupper($request->method());

        // Optional: /application wird nicht geprüft
        if (str($fullPath)->startsWith('/application')) {
            return $next($request);
        }

        $routeGroup = explode('/', trim($fullPath, '/'))[0] ?? null;

        if ($routeGroup === 'api') {
            // Überspringe 'api' Prefix
            $routeGroup = explode('/', trim(substr($fullPath, 4), '/'))[0] ?? null;
        }

        $routeFile = base_path('routes/meta/api/' . $routeGroup . '.php');



        if (!file_exists($routeFile)) {
            return response()->json([
                'status' => 404,
                'message' => 'Die Seite konnte nicht gefunden werden',
                'type' => 'error',
            ], 404);
        }


        $route_roles = require $routeFile;

        $data = [
            'to' => $fullPath,
            'method' => $method,
        ];

        $user = auth()->user();
        $result = $routeService->checkApiRoles($user, $data, $route_roles);

        switch ($result) {
            case RouteResult::ALLOWED:
                return $next($request); // Zugriff erlaubt

            case RouteResult::NOT_ALLOWED:
                $status = 403;
                $message = 'Sie dürfen diese Aktion nicht ausführen';
                break;

            case RouteResult::NOT_EXISTS:
                return $next($request); // Zugriff erlaubt
                break;

            case RouteResult::NOT_FOUND:
                $status = 404;
                $message = 'Fehlerhafte Routenprüfung (Route nicht gefunden)';
                break;

            default:
                $status = 500;
                $message = 'Unbekannter Fehler in ApiAllowed Middleware';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'type' => 'error',
        ], $status);
    }
}
