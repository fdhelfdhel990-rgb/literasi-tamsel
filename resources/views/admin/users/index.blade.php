@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('content')
<div class="admin-heading"><div><span class="eyebrow">PENGATURAN AKSES</span><h1>Kelola Admin &amp; Izin</h1><p>Akun dibuat Super Admin. Tidak tersedia pendaftaran publik.</p></div><a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ Tambah akun</a></div>
<section class="admin-panel">
    <div class="table-scroll">
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Email</th><th>Username</th><th>Peran</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($users as $account)
                    <tr>
                        <td class="table-title">{{ $account->name }}</td>
                        <td>{{ $account->email }}</td>
                        <td>{{ $account->username }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($account->role)) }}</td>
                        <td><span class="pill {{ $account->is_active ? 'published' : 'pending' }}">{{ $account->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="table-actions">
                            @if($account->role !== \App\Models\User::ROLE_SUPER_ADMIN)
                                <a href="{{ route('admin.users.edit', $account) }}">Edit</a>
                                @if($account->is_active)
                                    <form method="POST" action="{{ route('admin.users.destroy', $account) }}" onsubmit="return confirm('Nonaktifkan akun ini? Sesi berikutnya akan dicabut.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="table-delete" type="submit">Nonaktifkan</button>
                                    </form>
                                @endif
                            @else
                                <span>Pemilik</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</section>
<section class="admin-panel transfer-panel">
    <h2>Transfer Super Admin</h2>
    <p>Transfer bersifat transaksional. Akun ini akan diturunkan menjadi Admin dan sesi saat ini akan berakhir.</p>
    <form class="editor-grid" method="POST" action="{{ route('admin.users.transfer') }}" onsubmit="return confirm('Anda yakin memindahkan kepemilikan Super Admin?')">
        @csrf
        <label>Admin penerima
            <select name="target_user_id" required>
                <option value="">Pilih akun aktif</option>
                @foreach($transferTargets as $target)<option value="{{ $target->id }}">{{ $target->name }} ({{ $target->email }})</option>@endforeach
            </select>
        </label>
        <label>Kata sandi Anda<input type="password" name="current_password" autocomplete="current-password" required></label>
        <label>Konfirmasi ketik <strong>TRANSFER SUPER ADMIN</strong><input name="confirmation" required autocomplete="off"></label>
        <button class="btn btn-primary editor-wide" type="submit">Transfer kepemilikan</button>
    </form>
</section>
@endsection
