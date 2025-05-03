<?php

namespace Itstudioat\Spa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Itstudioat\Spa\Enums\RouteResult;
use Illuminate\Support\Facades\Redirect;
use Itstudioat\Spa\Services\RouteService;
use Symfony\Component\HttpFoundation\Response;

class WebAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $routeService = new RouteService();
        // e.g. 'admin/test_route/7' or homepage '/'
        $fullPath = '/' . ltrim($request->path(), '/');

        // /application wird nicht weiter geprüft
        if (str($fullPath)->startsWith('/application')) {
            return $next($request);
        }

        // Check if the path is just '/' (homepage)
        if ($fullPath === '/') {
            // Handle the homepage route here (optional)
            // If you don't need any special checks for the homepage, you can skip to the next middleware
            return $next($request);
        }

        // Extract 'admin' (first segment) to determine which meta file to load
        $routeGroup = explode('/', trim($fullPath, '/'))[0] ?? null;

        $routeFile = base_path('routes/meta/web/' . $routeGroup . '.php');


        if (!file_exists($routeFile)) {
            $status = 404;
            $message = 'Die Seite konnte nicht gefunden werden';

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'type' => 'error'
                ], $status);
            }

            return Redirect::to('/application/error?' . http_build_query([
                'status' => $status,
                'message' => $message,
                'type' => 'error',
            ]));
        }

        $route_roles = require base_path('routes/meta/web/' . $routeGroup . '.php');

        $data['to'] = $fullPath;
        $user = auth()->user();
        $result = $routeService->checkWebRoles($user, $data, $route_roles);


        switch ($result) {
            case RouteResult::ALLOWED:
                return $next($request); // Continue if allowed

            case RouteResult::NOT_ALLOWED:
                $status = 403;
                $message = 'Sie können auf diese Seite nicht zugreifen';
                break;

            case RouteResult::NOT_EXISTS:
                $status = 404;
                $message = 'Die Seite konnte nicht gefunden werden';
                break;

            case RouteResult::NOT_FOUND:
                $status = 404;
                $message = 'Die Seite ist nicht registriert: Programmierer verständigen';
                break;

            default:
                $status = 500;
                $message = 'Fehler in RouteAllowed Middleware';
        }

        // Redirect with error message if not allowed
        return Redirect::to('/application/error?' . http_build_query([
            'status' => $status,
            'message' => $message,
            'type' => 'error',
        ]));


        return $next($request);
    }
}
