<?php


namespace Itstudioat\Spa\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use Itstudioat\Spa\Traits\PaginationTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Itstudioat\Spa\Services\AdminService;
use Itstudioat\Spa\Http\Resources\Admin\UserResource;
use Itstudioat\Spa\Http\Requests\Admin\IndexUserRequest;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserRequest;
use Itstudioat\Spa\Http\Requests\Admin\SavePasswordRequest;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserWithCodeRequest;
use Itstudioat\Spa\Http\Requests\Admin\SavePasswordWithCodeRequest;

class UserController extends Controller
{

    use PaginationTrait;

    public function index(IndexUserRequest $request)
    {
        $auth_user = $this->hasRole(['admin']);
        $validated = $request->validated();
        $search_model = $validated['search_model'] ?? [];
        $query = User::query();

        // is_active
        if (isset($search_model['is_active'])) {
            if ($search_model['is_active'] === '1') {
                $query->where('is_active', true);
            } elseif ($search_model['is_active'] === '2') {
                $query->where('is_active', false);
            }
        }

        // is_confirmed (based on confirmed_at)
        if (isset($search_model['is_confirmed'])) {
            if ($search_model['is_confirmed'] === '1') {
                $query->whereNotNull('confirmed_at');
            } elseif ($search_model['is_confirmed'] === '2') {
                $query->whereNull('confirmed_at');
            }
        }

        // is_verified (based on email_verified_at)
        if (isset($search_model['is_verified'])) {
            if ($search_model['is_verified'] === '1') {
                $query->whereNotNull('email_verified_at');
            } elseif ($search_model['is_verified'] === '2') {
                $query->whereNull('email_verified_at');
            }
        }

        // is_2fa
        if (isset($search_model['is_2fa'])) {
            if ($search_model['is_2fa'] === '1') {
                $query->where('is_2fa', true);
            } elseif ($search_model['is_2fa'] === '2') {
                $query->where('is_2fa', false);
            }
        }


        // search_string (optional)
        if (!empty($search_model['search_string'])) {
            $search = $search_model['search_string'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }


        $pagination = UserResource::collection($query->paginate(config('spa.pagination')));
        return response()->json($this->makePagination($pagination), 200);
    }

    public function store(Request $request) {}

    public function show(User $user)
    {
        $auth_user = $this->hasRole(['admin']);
        return response()->json(new UserResource($user), 200);
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        $auth_user = $this->hasRole(['admin']);
        $validated = $request->validated();

        // Benutzer is_confirmed?
        if ($validated['is_confirmed']) {
            if (!$user->confirmed_at) {
                $validated['confirmed_at'] = now();
            }
        } else {
            $validated['confirmed_at'] = null;
        }
        unset($validated['is_confirmed']);

        // E-Mail is_validated
        if ($validated['is_verified']) {
            if (!$user->email_verified_at) {
                $validated['email_verified_at'] = now();
            }
        } else {
            $validated['email_verified_at'] = null;
        }
        unset($validated['is_verified']);

        $user->update($validated);
        return response()->json(new UserResource($user), 200);
    }


    public function destroy(User $user)
    {
        return response()->json(['message' => "Destroy {$id}"]);
    }


    public function updateProfile(UpdateUserRequest $request, User $user)
    {
        $auth_user = $this->hasRole(['admin']);
        $validated = $request->validated();

        if ($user->email != $validated['email']) {
            // Neue E-Mail-Adresse, die muss natürlich zunächst bestätigt werden
            $adminService = new AdminService();
            $adminService->sendEmailValidationToken(1, $user, $validated['email']);
            return response()->json(['answer' => 'INPUT_CODE', 'email' => $user->email, 'email_new' => $validated['email']]);
        }

        $user->update($validated);
        return response()->json(new UserResource($user), 200);
    }

    public function updateWithCode(UpdateUserWithCodeRequest $request)
    {
        $user = $this->hasRole(['admin']);
        $validated = $request->validated();

        if (!$user->checkToken2Fa($validated['token_2fa'])) abort(401, "Der Code ist falsch oder abgelaufen");

        $validated['email_verified_at'] = now();
        $user->update($validated);

        return response()->json(new UserResource($user), 200);
    }

    public function savePassword(SavePasswordRequest $request)
    {
        $user = $this->hasRole(['admin']);
        $validated = $request->validated();

        $adminService = new AdminService();

        $adminService->sendPasswordResetToken(1, $user, $user->email);

        $data = ['step' => 'PASSWORD_ENTER_TOKEN'];
        return response()->json($data, 200);
    }


    public function savePasswordWithCode(SavePasswordWithCodeRequest $request)
    {
        $user = $this->hasRole(['admin']);
        $validated = $request->validated();

        if (!$user->checkToken2Fa($validated['token_2fa'])) abort(401, "Kennwort setzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");

        $user->update(
            [
                'password' => Hash::make($validated['password']),
            ]
        );
    }
}
