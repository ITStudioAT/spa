<?php

namespace Itstudioat\Spa\Services;

use Itstudioat\Spa\Enums\RouteResult;

class RouteService
{
    public function checkWebRoles($user, $data, $route_roles): RouteResult
    {

        $toPath = $data['to'] ?? null; // actual route like /admin/test_route/7
        if (! $toPath) {
            return RouteResult::NOT_FOUND;
        }

        $rolesMap = $route_roles['roles'] ?? [];

        $matchedKey = null;

        foreach ($rolesMap as $pattern => $roles) {
            if (self::matchesPath($pattern, $toPath)) {
                $matchedKey = $pattern;

                break;
            }
        }

        if (! $matchedKey) {
            return RouteResult::NOT_EXISTS;
        }

        $requiredRoles = $rolesMap[$matchedKey];

        if (empty($requiredRoles)) {
            return RouteResult::ALLOWED;
        }

        if (! $user) {
            return RouteResult::NOT_ALLOWED;
        }

        $requiredRoles[] = 'super_admin';

        if ($user->hasAnyRole($requiredRoles)) {
            return RouteResult::ALLOWED;
        }

        return RouteResult::NOT_ALLOWED;
    }

    public function checkApiRoles($user, $data, $route_roles): RouteResult
    {

        $toPath = $data['to'] ?? null;  // z.B. '/api/admin/users/7'
        $method = strtoupper($data['method'] ?? 'GET'); // HTTP-Methode

        if (! $toPath) {
            return RouteResult::NOT_FOUND;
        }

        // Entferne '/api' aus dem Pfad, wenn es vorhanden ist
        if (str_starts_with($toPath, '/api')) {
            $toPath = substr($toPath, 4); // Entferne die ersten 4 Zeichen ('/api')
        }

        $rolesMap = $route_roles['roles'] ?? [];
        $routeKey = $method . ' ' . $toPath; // z.B. "POST /admin/users/7"

        $matchedKey = null;

        // Suche nach einer Übereinstimmung der Route und Methode
        foreach ($rolesMap as $pattern => $roles) {
            if (self::matchesRoute($pattern, $routeKey)) {
                $matchedKey = $pattern;

                break;
            }
        }

        // Wenn keine Übereinstimmung gefunden wurde
        if (! $matchedKey) {
            return RouteResult::NOT_EXISTS;
        }

        $requiredRoles = $rolesMap[$matchedKey];

        // Wenn keine Rollen erforderlich sind, erlauben
        if (empty($requiredRoles)) {
            return RouteResult::ALLOWED;
        }

        // Wenn kein Benutzer eingeloggt ist, nicht erlauben
        if (! $user) {
            return RouteResult::NOT_ALLOWED;
        }

        // Wenn super_admin in der Konfiguration gesetzt ist, füge ihn zu den erforderlichen Rollen hinzu
        $requiredRoles[] = 'super_admin';

        // Überprüfe, ob der Benutzer die erforderlichen Rollen hat
        if ($user->hasAnyRole($requiredRoles)) {
            return RouteResult::ALLOWED;
        }

        return RouteResult::NOT_ALLOWED;
    }

    private static function matchesPath(string $pattern, string $path): bool
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));

        if (count($patternParts) !== count($pathParts)) {
            return false;
        }

        foreach ($patternParts as $i => $part) {
            if (str_starts_with($part, ':')) {
                continue; // wildcard segment, like :id
            }

            if ($part !== $pathParts[$i]) {
                return false;
            }
        }

        return true;
    }

    private static function matchesRoute(string $pattern, string $routeKey): bool
    {
        $patternParts = explode(' ', $pattern, 2);
        $routeParts = explode(' ', $routeKey, 2);

        if (count($patternParts) < 2 || count($routeParts) < 2) {
            return false; // Beide müssen "METHOD PATH" Format haben
        }

        [$patternMethod, $patternPath] = $patternParts;
        [$routeMethod, $routePath] = $routeParts;

        // Methode vergleichen (z.B. GET vs POST), '*' erlaubt alles
        if ($patternMethod !== '*' && strtoupper($patternMethod) !== strtoupper($routeMethod)) {
            return false;
        }

        // Path vergleichen (mit Wildcards wie :id)
        return self::matchesPath($patternPath, $routePath);
    }
}
