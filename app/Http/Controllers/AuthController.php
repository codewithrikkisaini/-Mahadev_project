<?php

namespace App\Http\Controllers;

use App\Models\CommitteeNotification;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        // Also check if member entered Member Code like MSC00001
        if ($loginType === 'mobile' && preg_match('/^[A-Za-z0-9\-]+$/', $credentials['login']) && !is_numeric($credentials['login'])) {
            $member = Member::where('member_code', strtoupper(trim($credentials['login'])))->first();
            if ($member && $member->user) {
                $user = $member->user;
                if (Hash::check($credentials['password'], $user->password)) {
                    if ($user->status !== 'active') {
                        return back()->withInput()->withErrors(['login' => 'Your account is inactive or pending approval.']);
                    }
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    return redirect()->intended(route('member.dashboard'));
                }
            }
        }

        $user = User::where($loginType, $credentials['login'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->status !== 'active') {
                return back()->withInput()->withErrors(['login' => 'Your account is inactive or pending approval.']);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('member.dashboard'));
        }

        return back()->withInput()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show member registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        $monthlyAmount = Setting::getMonthlyAmount();
        $committeeName = Setting::get('committee_name', 'Mandir Seva Committee');

        return view('auth.register', compact('monthlyAmount', 'committeeName'));
    }

    /**
     * Handle member registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,mobile'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['nullable', 'date'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
                'status' => 'active',
            ]);

            $memberCode = Member::generateNextCode();
            $joiningDate = $validated['joining_date'] ?? Carbon::today()->format('Y-m-d');

            $member = Member::create([
                'user_id' => $user->id,
                'member_code' => $memberCode,
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'joining_date' => $joiningDate,
                'status' => 'active',
            ]);

            // Welcome notification
            CommitteeNotification::sendToUser(
                $user->id,
                'Welcome to Mandir Seva Committee! 🙏',
                "Your registration is successful. Your Member ID is {$memberCode}. Monthly Seva Amount: ₹" . Setting::getMonthlyAmount(),
                'general',
                route('member.dashboard')
            );

            return $user;
        });

        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Registration successful! Welcome to Mandir Seva Committee.');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
