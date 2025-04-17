<?php

namespace Itstudioat\Spa\Http\Controllers\Admin;

use Composer\InstalledVersions;
use Illuminate\Routing\Controller;
use Itstudioat\Spa\Services\AdminService;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep1Request;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep2Request;
use Itstudioat\Spa\Http\Requests\Admin\LoginStep3Request;


class AdminController extends Controller
{

    public function index()
    {
        return view('admin');
    }

    public function config()
    {

        $data = [
            'logo' => config('spa.logo', ''),
            'copyright' => config('spa.copyright', ''),
            'timeout' => config('spa.timeout', 3000),
            'title' => config('spa.title', 'Fresh Laravel'),
            'company' => config('spa.company', 'ItStudio.at'),
            'version' => InstalledVersions::getPrettyVersion('itstudioat/spa'),
        ];

        return response()->json($data, 200);
    }


    public function loginStep1(LoginStep1Request $request)
    {
        $adminService = new AdminService();
        $validated = $request->validated();

        $user = $adminService->checkUserLogin($validated['email']);
        return response()->json(['step' => 1], 200);
    }

    public function loginStep2(LoginStep2Request $request)
    {
        $adminService = new AdminService();
        $validated = $request->validated();

        $user = $adminService->checkUserLogin($validated['data']['email'], $validated['data']['password']);

        if ($user->is_fa2) {
            // Bei Benutzer ist 2-Faktoren-Authentifizierung aktiviert
            $adminService->continueLoginFor2FaUser($user);
            return response()->json(['step' => 2], 200);
        } else {
            // Keine 2-Faktoren-Authentifizierung ==> Login fertig
            $data = [
                'step' => 0,
                'auth' => true,
                'user' => $user,
            ];

            return response()->json($data, 200);
        }
    }

    public function loginStep3(LoginStep3Request $request) {}
}
