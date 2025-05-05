<?php

namespace Itstudioat\Spa\Http\Controllers\Admin;

use App\Models\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Itstudioat\Spa\Traits\PaginationTrait;
use Itstudioat\Spa\Http\Resources\Admin\RoleResource;
use Itstudioat\Spa\Http\Requests\Admin\IndexRoleRequest;
use Itstudioat\Spa\Http\Requests\Admin\StoreRoleRequest;
use Itstudioat\Spa\Http\Requests\Admin\UpdateRoleRequest;

class RoleController extends Controller
{
    use PaginationTrait;

    public function index(IndexRoleRequest $request)
    {
        if (! $auth_user = $this->userHasRole(['super_admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $validated = $request->validated();
        $search_model = $validated['search_model'] ?? [];
        $query = Role::orderBy('name');

        // search_string (optional)
        if (! empty($search_model['search_string'])) {
            $search = $search_model['search_string'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $pagination = RoleResource::collection($query->paginate(config('spa.pagination')));

        return response()->json($this->makePagination($pagination), 200);
    }

    public function store(StoreRoleRequest $request)
    {

        if (! $auth_user = $this->userHasRole(['super_admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $validated = $request->validated();

        $role = Role::create($validated);

        return response()->json(new RoleResource($role), 200);
    }

    public function show(Role $role)
    {
        if (! $auth_user = $this->userHasRole(['super_admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }

        return response()->json(new RoleResource($role), 200);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $auth_user = $this->userHasRole(['super_admin']);
        $validated = $request->validated();

        $role->update($validated);

        return response()->json(new RoleResource($role), 200);
    }

    public function destroy(Role $role)
    {
        if (! $auth_user = $this->userHasRole(['super_admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }

        info($user->id);
        info($auth_user->id);

        if (!$role->shouldDelete()) {
            abort(403, 'Bei Löschen ist ein Fehler aufgetreten. Möglicherweise gibt es abhängige Daten.');
        }

        return response()->noContent();
    }

    public function destroyMultiple(Request $request)
    {
        if (! $auth_user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $ids = $request->all();

        User::whereIn('id', $ids)->each(function ($user) use ($auth_user) {
            if ($user->id == $auth_user->id) {
                abort(403, 'Man kann sich selbst nicht löschen');
            }
            if (! $user->shouldDelete()) {
                abort(403, 'Bei Löschen ist ein Fehler aufgetreten. Möglicherweise gibt es abhängige Daten.');
            }
        });

        return response()->noContent();
    }

    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        if (! $auth_user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
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
        if (! $user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $validated = $request->validated();
        if (! $user->checkToken2Fa($validated['token_2fa'])) {
            abort(401, 'Der Code ist falsch oder abgelaufen');
        }
        $validated['email_verified_at'] = now();
        $user->update($validated);

        return response()->json(new UserResource($user), 200);
    }

    public function savePassword(SavePasswordRequest $request)
    {
        if (! $user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $validated = $request->validated();

        $adminService = new AdminService();

        $adminService->sendPasswordResetToken(1, $user, $user->email);

        $data = ['step' => 'PASSWORD_ENTER_TOKEN'];

        return response()->json($data, 200);
    }

    public function savePasswordWithCode(SavePasswordWithCodeRequest $request)
    {
        if (! $user = $this->userHasRole(['admin'])) {
            abort(403, 'Sie haben keine Berechtigung');
        }
        $validated = $request->validated();

        if (! $user->checkToken2Fa($validated['token_2fa'])) {
            abort(401, 'Kennwort speichern funktioniert nicht. Code falsch oder Zeit abgelaufen.');
        }

        $user->update(
            [
                'password' => Hash::make($validated['password']),
            ]
        );
    }
}
