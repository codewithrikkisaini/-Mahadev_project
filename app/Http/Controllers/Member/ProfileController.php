<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $member = $user->member;
        $member->load(['approvedPayments', 'payments']);
        $summary = $member->payment_summary;

        return view('member.profile', compact('user', 'member', 'summary'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $member = $user->member;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/', Rule::unique('users', 'mobile')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'address' => ['nullable', 'string', 'max:500'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $userUpdates = [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['new_password'])) {
            $userUpdates['password'] = Hash::make($validated['new_password']);
        }

        $user->update($userUpdates);

        $member->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? null,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
