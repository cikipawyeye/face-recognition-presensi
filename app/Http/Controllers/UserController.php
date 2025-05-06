<?php

namespace App\Http\Controllers;

use App\Constants\PermissionConstant as Permission;
use App\Enums\RoleEnum;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SaveUserRequest;
use App\Http\Services\FaceRecognitionService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(sprintf('permission:%s', Permission::BROWSE_USERS))->only('index');
        $this->middleware(sprintf('permission:%s', Permission::READ_USER))->only('show');
        $this->middleware(sprintf('permission:%s', Permission::ADD_USER))->only('store');
        $this->middleware(sprintf('permission:%s', Permission::EDIT_USER))->only('update');
        $this->middleware(sprintf('permission:%s', Permission::DELETE_USER))->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', RoleEnum::User->value);
            })
            ->search($request->input('search'))
            ->latest();
        $paginate = $request->boolean('paginate', true);

        return inertia('user/Index', [
            'users' => Inertia::defer(fn() => ! $paginate
                ? $query->get()
                : $query->paginate()->withQueryString()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());
        $user->assignRole(RoleEnum::User->value);

        $photos = collect($request->file('photos'));

        try {
            $photos->each(function (UploadedFile $photo, $index) {
                /** @disregard */
                $response = FaceRecognitionService::checkFacePresence($photo);
                $body = $response->json();
    
                if (!$response->successful()) {
                    $message = isset($body['detail']) ? $body['detail'] : 'Failed to detect face.';
    
                    throw new \LogicException('Gambar ' . ($index + 1) . ': ' . $message);
                }
            });
    
            DB::transaction(function () use ($photos, $user) {
                $photos->each(fn (UploadedFile $photo) => $user->addMedia($photo)->toMediaCollection('face-reference', 'public'));
            });
        } catch (\Throwable $th) {
            return back()
                ->withErrors([
                    'photos' => $th->getMessage() . ' Silahkan masukkan ulang.'
                ]);
        }

        return redirect()
            ->route('users.show', $user)
            ->with('success', __('app.created_data', ['data' => __('app.user')]));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        return inertia('user/Show', [
            'user' => $user->append('photo_urls'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only('name', 'email'));
        $user->assignRole(RoleEnum::User->value);

        return redirect()
            ->route('users.show', $user)
            ->with('success', __('app.updated_data', ['data' => __('app.user')]));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', __('app.deleted_data', ['data' => __('app.user')]));
    }

    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make($request->get('password')),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', __('app.deleted_data', ['data' => __('app.user')]));
    }
}
