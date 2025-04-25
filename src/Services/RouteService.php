<?php

namespace Itstudioat\Spa\Services;

use app\Models\User;
use Spatie\Permission\Models\Role;
use Itstudioat\Spa\Enums\RouteResult;



class RouteService
{


    public function checkRoles($user, $data, $route_roles): RouteResult
    {

        $toPath = $data['to'] ?? null; // actual route like /admin/test_route/7
        if (!$toPath) {
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

        if (!$matchedKey) {
            return RouteResult::NOT_EXISTS;
        }

        $requiredRoles = $rolesMap[$matchedKey];


        if (empty($requiredRoles)) {
            return RouteResult::ALLOWED;
        }

        if (!$user) {
            return RouteResult::NOT_ALLOWED;
        }


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
}
