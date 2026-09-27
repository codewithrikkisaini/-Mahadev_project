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
                    if ($user->status === 'pending') {
                        return back()->withInput()->withErrors(['login' => 'Your registration (Member ID: ' . $member->member_code . ') is pending Super Admin approval. Please wait for approval before logging in. 🙏']);
                    }
                    if ($user->status !== 'active') {
                        return back()->withInput()->withErrors(['login' => 'Your account is inactive. Please contact the Mandir Seva Committee.']);
                    }
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    return redirect()->intended(route('member.dashboard'));
                }
            }
        }

        $user = User::where($loginType, $credentials['login'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->status === 'pending') {
                $memberCode = $user->member ? $user->member->member_code : 'Pending';
                return back()->withInput()->withErrors(['login' => 'Your registration (' . $memberCode . ') is pending Super Admin approval. Please wait for activation before logging in. 🙏']);
            }
            if ($user->status !== 'active') {
                return back()->withInput()->withErrors(['login' => 'Your account is inactive. Please contact the Mandir Seva Committee.']);
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

        $memberCode = Member::generateNextCode();
        $joiningDate = $validated['joining_date'] ?? Carbon::today()->format('Y-m-d');

        $user = DB::transaction(function () use ($validated, $memberCode, $joiningDate) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
                'status' => 'pending', // Pending Super Admin approval
            ]);

            $member = Member::create([
                'user_id' => $user->id,
                'member_code' => $memberCode,
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'joining_date' => $joiningDate,
                'status' => 'pending', // Pending Super Admin approval
            ]);

            // Welcome notification to member
            CommitteeNotification::sendToUser(
                $user->id,
                'Registration Submitted 🙏',
                "Your registration for {$member->name} (Member ID: {$memberCode}) has been received and is waiting for Super Admin approval.",
                'general',
                route('login')
            );

            // Notification to Super Admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                CommitteeNotification::sendToUser(
                    $admin->id,
                    'New Member Join Request 🙏',
                    "{$member->name} ({$memberCode}, Mobile: {$member->mobile}) has registered and is waiting for your approval.",
                    'member_pending',
                    route('admin.members.index', ['status' => 'pending'])
                );
            }

            return $user;
        });

        return redirect()->route('login')->with('success', "Registration request submitted successfully! Your Member ID is {$memberCode}. Your account is currently pending Super Admin approval. You will be able to log in once approved. 🙏");
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
