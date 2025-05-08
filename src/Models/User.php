<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Itstudioat\Spa\Traits\UserTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Itstudioat\Spa\Notifications\StandardEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use UserTrait;
    use HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $guard_name = 'web';

    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function shouldDelete(): bool
    {
        $this->delete();
        return true;
    }

    public function sendVerificationEmail()
    {
        $uuid = $this->generateUuid();

        $data = [
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => 'E-Mail-Verifikation',
            'markdown' => 'spa::mails.admin.sendEmailVerification',
            'url' => $data['url']  = config('app.url') . "/admin/email_verification?email=" . $this->email . "&uuid=" . $uuid,
            'token-expire-time' => config('spa.token_expire_time'),
        ];

        Notification::route('mail', $this->email)->notify(new StandardEmail($data));
    }

    public function generateUuid(): string
    {
        $this->uuid = Str::uuid();
        $this->uuid_at = now();
        $this->save();
        return $this->uuid;
    }

    public function checkUuid($uuid): bool
    {
        if ($this->uuid !== $uuid || !$this->uuid_at) return false;
        return $this->uuid_at > now()->subMinutes(config('spa.token_expire_time', 120));
    }

    public function emailVerified(): bool
    {
        $this->email_verified_at = now();
        $this->uuid = null;
        $this->uuid_at = null;
        $this->save();
        return true;
    }
}
