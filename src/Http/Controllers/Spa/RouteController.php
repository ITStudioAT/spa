<?php

namespace Itstudioat\Spa\Http\Controllers\Spa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itstudioat\Spa\Enums\RouteResult;
use Itstudioat\Spa\Http\Requests\Spa\RouteAllowedRequest;
use Itstudioat\Spa\Services\RouteService;

class RouteController extends Controller
{

    public function isRouteAllowed(RouteAllowedRequest $request)
    {
        // Getting the route request from routes/admin.js
        // Checking, if the user is permitted to go the route
        // return ok, or abort

        $user = auth()->user();

        $data = $request->validated()['data'];

        $routeService = new RouteService();

        $route_roles = require base_path('routes/meta/web/' . $data['route'] . '.php');

        $result = $routeService->checkWebRoles($user, $data, $route_roles);

        if ($result == RouteResult::ALLOWED) return response()->json(['message' => 'Success'], 200);
        if ($result == RouteResult::NOT_ALLOWED) abort(403, 'Sie können auf diese Seite nicht zugreifen');
        if ($result == RouteResult::NOT_EXISTS) abort(404, 'Die Seite konnte nicht gefunden werden');
        if ($result == RouteResult::NOT_FOUND) abort(404, 'Die Seite ist nicht registriert: Programmierer verständigen');

        abort(500, "Fehler in RouteController/isRouteAllowed");
    }
}
