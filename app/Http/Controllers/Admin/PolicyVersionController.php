<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PolicyVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PolicyVersionController extends Controller
{
    public function index(): View
    {
        $policies = PolicyVersion::orderByDesc('created_at')->get();
        return view('admin.policies.index', compact('policies'));
    }

    public function create(): View
    {
        return view('admin.policies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'policy_key' => ['required', 'string', 'max:100'],
            'version' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:191'],
            'content' => ['required', 'string'],
        ]);

        PolicyVersion::create([
            ...$request->only('policy_key', 'version', 'title', 'content'),
            'created_by_admin_id' => Auth::guard('admin')->id(),
        ]);

        return redirect()->route('admin.policies.index')->with('success', 'Kebijakan berhasil dibuat.');
    }

    public function show(PolicyVersion $policy): View
    {
        return view('admin.policies.show', compact('policy'));
    }

    public function publish(PolicyVersion $policy): RedirectResponse
    {
        DB::transaction(function () use ($policy) {
            // Unset previous current for same key
            PolicyVersion::where('policy_key', $policy->policy_key)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $policy->update([
                'is_current' => true,
                'published_at' => now(),
            ]);
        });

        return redirect()->route('admin.policies.index')->with('success', 'Kebijakan berhasil dipublikasikan.');
    }
}
