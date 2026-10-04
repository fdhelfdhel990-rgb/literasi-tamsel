<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteContentRequest;
use App\Models\SiteSetting;

class SiteContentController extends Controller
{
    public function edit()
    {
        abort_unless(request()->user()->hasPermission('site_content.manage'), 403);

        return view('admin.site-content', ['community' => SiteSetting::valueFor('community', $this->defaults())]);
    }

    public function update(SiteContentRequest $request)
    {
        $community = SiteSetting::valueFor('community', $this->defaults());
        $values = $request->validated();

        foreach ($values['impact'] as $index => $metric) {
            $community['impact'][$index]['value'] = (int) $metric['value'];
        }

        $community['contact'] = $values['contact'];
        $community['social'] = $values['social'] ?? [];
        $community['social_visibility'] = [];
        foreach (['youtube', 'instagram', 'tiktok', 'facebook'] as $key) {
            $community['social_visibility'][$key] = (bool) ($values['social_visibility'][$key] ?? false);
        }
        $community['profile'] = $values['profile'];

        SiteSetting::query()->updateOrCreate(
            ['key' => 'community'],
            ['value' => $community, 'updated_by' => $request->user()->id],
        );

        return redirect()->route('admin.site-content.edit')->with('status', 'Konten komunitas berhasil disimpan.');
    }

    private function defaults(): array
    {
        return [
            'impact' => [
                ['label' => 'Berdampak Positif ke', 'value' => 0, 'prefix' => '>', 'suffix' => '', 'unit' => 'Anak'],
                ['label' => 'Koleksi Bacaan', 'value' => 0, 'prefix' => '±', 'suffix' => '', 'unit' => 'Buku'],
                ['label' => 'Sukarelawan Aktif', 'value' => 0, 'prefix' => '', 'suffix' => '', 'unit' => 'Remaja'],
                ['label' => 'Perpustakaan Desa', 'value' => 0, 'prefix' => '', 'suffix' => '', 'unit' => 'Lokasi Binaan'],
            ],
            'contact' => ['whatsapp' => '', 'email' => '', 'location' => ''],
            'social' => [],
            'social_visibility' => [],
            'profile' => [
                'name' => 'Komunitas Literasi Remaja Tambun Selatan',
                'home_intro' => '',
                'about' => '',
                'mission' => '',
            ],
        ];
    }
}