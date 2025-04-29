<?php


namespace Itstudioat\Spa\Http\Controllers\Admin;

use app\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Itstudioat\Spa\Services\AdminService;
use Itstudioat\Spa\Http\Resources\Admin\UserResource;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserRequest;
use Itstudioat\Spa\Http\Requests\Admin\SavePasswordRequest;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserWithCodeRequest;
use Itstudioat\Spa\Http\Requests\Admin\SavePasswordWithCodeRequest;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(['message' => 'Index']);
    }

    public function store(Request $request) {}

    public function show($id)
    {

        $user = $this->hasRole(['admin']);
        if ($user->id != $id) abort(403, 'Sie wollen einen falschen Benutzer abfragen.');

        return response()->json(new UserResource($user), 200);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->hasRole(['admin']);
        $validated = $request->validated();

        info($validated);

        if ($user->email != $validated['email']) {
            // Neue E-Mail-Adresse, die muss natürlich zunächst bestätigt werden
            $adminService = new AdminService();
            $adminService->sendEmailValidationToken(1, $user, $validated['email']);
            return response()->json(['answer' => 'INPUT_CODE', 'email' => $user->email, 'email_new' => $validated['email']]);
        }

        $user->update($validated);

        return response()->json(new UserResource($user), 200);
    }

    public function destroy($id)
    {
        return response()->json(['message' => "Destroy {$id}"]);
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
