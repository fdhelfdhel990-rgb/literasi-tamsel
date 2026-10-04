@extends('layouts.admin')
@section('title', $account->exists ? 'Edit Admin' : 'Tambah Admin')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">PENGATURAN AKSES</span><h1>{{ $account->exists ? 'Edit akun admin' : 'Tambah akun admin' }}</h1><p>Sub-Admin hanya mendapat izin yang dipilih di bawah.</p></div></div>
<form class="admin-panel admin-form" method="POST" action="{{ $account->exists ? route('admin.users.update', $account) : route('admin.users.store') }}">
    @csrf
    @if($account->exists) @method('PUT') @endif
    <div class="editor-grid">
        <label>Nama<input name="name" value="{{ old('name', $account->name) }}" required maxlength="255"></label>
        <label>Email<input name="email" type="email" value="{{ old('email', $account->email) }}" autocomplete="off" required maxlength="255"></label>
        <label>Peran<select name="role" id="adminRole" required><option value="admin" @selected(old('role', $account->role ?: 'admin') === 'admin')>Admin</option><option value="sub_admin" @selected(old('role', $account->role) === 'sub_admin')>Sub-Admin</option></select></label>
        <label>Kata sandi {{ $account->exists ? '(kosongkan bila tidak diubah)' : '' }}<input name="password" type="password" autocomplete="new-password" {{ $account->exists ? '' : 'required' }} minlength="12"></label>
        <label>Konfirmasi kata sandi<input name="password_confirmation" type="password" autocomplete="new-password" {{ $account->exists ? '' : 'required' }} minlength="12"></label>
        <input type="hidden" name="is_active" value="0">
        <label class="check-label"><input name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', $account->is_active ?? true))> Akun aktif</label>
        <fieldset class="editor-wide permission-fields" data-permission-fields>
            <legend>Izin Sub-Admin</legend>
            <div class="editor-grid">
                @foreach($permissions as $permission)
                    <label class="check-label"><input type="checkbox" name="permissions[]" value="{{ $permission }}" @checked(in_array($permission, old('permissions', $account->permissions ?? []), true))>{{ ucwords(str_replace(['.', '_'], ' ', $permission)) }}</label>
                @endforeach
            </div>
        </fieldset>
    </div>
    @if($errors->any())<p class="login-error" role="alert">{{ $errors->first() }}</p>@endif
    <div class="editor-actions"><button class="btn btn-primary" type="submit">Simpan akun</button><a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Batal</a></div>
</form>
@endsection