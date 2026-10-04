<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JoinCardsRequest;
use App\Models\JoinCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JoinCardController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->hasPermission('join_cards.manage'), 403);

        return view('admin.join-cards', ['cards' => JoinCard::query()->orderBy('position')->get()]);
    }

    public function update(JoinCardsRequest $request)
    {
        $cards = collect($request->validated('cards'))->keyBy('key');
        $required = ['volunteer', 'volunteer_event', 'collaboration'];

        if ($cards->keys()->sort()->values()->all() !== collect($required)->sort()->values()->all()) {
            throw ValidationException::withMessages(['cards' => 'Ketiga kartu Join Us harus dikirim tepat satu kali.']);
        }

        DB::transaction(function () use ($cards, $required): void {
            foreach ($required as $position => $key) {
                $data = $cards[$key];
                JoinCard::query()->updateOrCreate(['key' => $key], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'form_url' => $data['form_url'] ?? null,
                    'is_open' => (bool) ($data['is_open'] ?? false),
                    'closed_description' => $data['closed_description'],
                    'button_label' => $data['button_label'],
                    'position' => $position,
                ]);
            }
        });

        return redirect()->route('admin.join-cards.index')->with('status', 'Kartu Join Us berhasil disimpan.');
    }
}