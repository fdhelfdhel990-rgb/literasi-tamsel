<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\TransferSuperAdminRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::query()->orderBy('role')->orderBy('name')->paginate(20),
            'permissions' => User::CONTENT_PERMISSIONS,
            'superAdmins' => User::query()->where('role', User::ROLE_SUPER_ADMIN)->where('is_active', true)->get(),
            'transferTargets' => User::query()->where('is_active', true)->where('role', '!=', User::ROLE_SUPER_ADMIN)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.users.form', ['account' => new User(), 'permissions' => User::CONTENT_PERMISSIONS]);
    }

    public function store(StoreAdminUserRequest $request)
    {
        $data = $request->validated();
        $data['permissions'] = $data['role'] === User::ROLE_SUB_ADMIN ? array_values($data['permissions'] ?? []) : null;
        User::query()->create($data);

        return redirect()->route('admin.users.index')->with('status', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.form', ['account' => $user, 'permissions' => User::CONTENT_PERMISSIONS]);
    }

    public function update(UpdateAdminUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['permissions'] = $data['role'] === User::ROLE_SUB_ADMIN ? array_values($data['permissions'] ?? []) : null;
        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('status', 'Akun dinonaktifkan.');
    }

    public function transfer(TransferSuperAdminRequest $request)
    {
        $actorId = $request->user()->id;
        $targetId = (int) $request->validated('target_user_id');
        $this->authorize('transferSuperAdmin', User::query()->findOrFail($targetId));

        DB::transaction(function () use ($actorId, $targetId): void {
            $actor = User::query()->lockForUpdate()->findOrFail($actorId);
            $target = User::query()->lockForUpdate()->findOrFail($targetId);

            if (! $actor->isSuperAdmin() || ! $target->is_active || $target->role === User::ROLE_SUPER_ADMIN) {
                throw ValidationException::withMessages(['target_user_id' => 'Transfer Super Admin tidak dapat dilakukan.']);
            }

            $otherSuperAdmins = User::query()->where('role', User::ROLE_SUPER_ADMIN)->where('id', '!=', $actorId)->count();
            if ($otherSuperAdmins > 0) {
                throw ValidationException::withMessages(['target_user_id' => 'Pastikan hanya ada satu Super Admin sebelum melakukan transfer.']);
            }

            $target->update(['role' => User::ROLE_SUPER_ADMIN, 'permissions' => null]);
            $actor->update(['role' => User::ROLE_ADMIN, 'permissions' => null]);
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Kepemilikan Super Admin berhasil dipindahkan. Silakan masuk kembali.');
    }
}