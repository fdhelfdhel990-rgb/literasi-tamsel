<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username')->nullable()->after('email');
        });

        $used = [];
        DB::table('users')->orderBy('id')->select(['id', 'email', 'name'])->get()->each(function ($user) use (&$used): void {
            $source = Str::before((string) $user->email, '@') ?: (string) $user->name;
            $base = Str::slug($source, '_') ?: 'admin';
            $candidate = Str::limit($base, 40, '');
            $suffix = 1;

            while (isset($used[$candidate]) || DB::table('users')->where('username', $candidate)->exists()) {
                $tail = '_'.$suffix++;
                $candidate = Str::limit($base, 40 - strlen($tail), '').$tail;
            }

            $used[$candidate] = true;
            DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
