<?php

namespace Itstudioat\Spa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Notification;
use Itstudioat\Spa\Notifications\StandardEmail;


class AdminController extends Controller
{
    public function index()
    {
        $data = [
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => 'Code für Login',
            'markdown' => 'spa::mails.admin.login2FaCode',
            'token_2fa' => 'abcdef',
            'token-expire-time' => config('spa.token-expire-time'),
        ];

        Notification::route('mail', 'kron@naturwelt.at')->notify(new StandardEmail($data));
    }
}
