<?php


namespace Itstudioat\Spa\Http\Controllers\Admin;

use app\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Itstudioat\Spa\Http\Resources\Admin\UserResource;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserRequest;
use Itstudioat\Spa\Http\Requests\Admin\UpdateUserWithCodeRequest;
use Itstudioat\Spa\Services\AdminService;

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


        if ($user->email != $validated['email']) {
            // Neue E-Mail-Adresse, die muss natürlich zunächst bestätigt werden
            $adminService = new AdminService();
            $adminService->sendEmailValidationToken(1, $user, $validated['email']);
            return response()->json(['answer' => 'input_code', 'email' => $user->email, 'email_new' => $validated['email']]);
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

        info($validated);
        info("check: " . $user->checkToken2Fa($validated['token_2fa']));

        if (!$user->checkToken2Fa($validated['token_2fa'])) abort(401, "Der Code ist falsch oder abgelaufen");

        $user->update($validated);
        return response()->json(new UserResource($user), 200);
    }
}
