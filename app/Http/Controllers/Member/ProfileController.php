<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Support\Phone\PhoneNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('member.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'string', 'max:32'],
        ]);

        auth()->user()->update([
            'name' => $validated['name'],
            'whatsapp' => PhoneNormalizer::normalize($validated['whatsapp']),
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
