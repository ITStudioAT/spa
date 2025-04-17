<?php

namespace Itstudioat\Spa\Http\Controllers\Homepage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomepageController extends Controller
{

    public function index()
    {
        return view('homepage');
    }

    public function config()
    {

        info("da");
        $data = [
            'logo' => config('spa.logo', ''),
            'copyright' => config('spa.copyright', ''),
            'timeout' => config('spa.copyright', 3000),
            'title' => config('spa.title', 'Fresh Laravel'),
            'company' => config('spa.company', 'ItStudio.at'),
        ];

        return response()->json($data, 200);
    }
}
