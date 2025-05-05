<?php

namespace Itstudioat\Spa\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Composer\InstalledVersions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Itstudioat\Spa\Services\AdminService;
use Itstudioat\Spa\Services\AdminNavigationService;
use Itstudioat\Spa\Http\Resources\Admin\UserResource;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep1Request;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep2Request;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep3Request;
use Itstudioat\Spa\Http\Requests\Admin\RegisterStep1Request;
use Itstudioat\Spa\Http\Requests\Admin\RegisterStep2Request;
use Itstudioat\Spa\Http\Requests\Admin\RegisterStep3Request;
use Itstudioat\Spa\Http\Requests\Admin\PasswordUnknownStep1Request;
use Itstudioat\Spa\Http\Requests\Admin\PasswordUnknownStep2Request;
use Itstudioat\Spa\Http\Requests\Admin\PasswordUnknownStep3Request;
use Itstudioat\Spa\Http\Requests\Admin\PasswordUnknownStep4Request;


class NavigationController extends Controller
{


    public function profileMenu()
    {
        $navigationService = new AdminNavigationService();

        if (! $auth_user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }

        $data = [
            'menu' =>  $navigationService->profileMenu(),
        ];

        return response()->json($data, 200);
    }


    public function userMenu()
    {
        $navigationService = new AdminNavigationService();

        if (! $auth_user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }

        $data = [
            'menu' =>  $navigationService->userMenu(),
            'selection' => $navigationService->userSelection(),
        ];

        return response()->json($data, 200);
    }
}
