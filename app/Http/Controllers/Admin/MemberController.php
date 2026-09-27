<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of members with search and filter.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Member::with(['user', 'approvedPayments', 'payments']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'inactive', 'pending'])) {
            $query->where('status', $status);
        }

        $members = $query->orderBy('member_code', 'asc')->paginate(15)->withQueryString();

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $inactiveMembers = Member::where('status', 'inactive')->count();
        $pendingMembers = Member::where('status', 'pending')->count();

        return view('admin.members.index', compact(
            'members',
            'search',
            'status',
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'pendingMembers'
        ));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $nextCode = Member::generateNextCode();
        $defaultMonthlyAmount = Setting::getMonthlyAmount();

        return view('admin.members.create', compact('nextCode', 'defaultMonthlyAmount'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,mobile'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'member_code' => ['nullable', 'string', 'unique:members,member_code'],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,pending'],
            'custom_monthly_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $memberCode = !empty($validated['member_code'])
            ? strtoupper(trim($validated['member_code']))
            : Member::generateNextCode();

        $email = $validated['email'] ?? strtolower($memberCode) . '@mandirseva.local';
        $password = $validated['password'] ?? '123456';

        DB::transaction(function () use ($validated, $memberCode, $email, $password) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'mobile' => $validated['mobile'],
                'password' => Hash::make($password),
                'role' => 'member',
                'status' => $validated['status'],
            ]);

            Member::create([
                'user_id' => $user->id,
                'member_code' => $memberCode,
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'email' => $email,
                'address' => $validated['address'] ?? null,
                'joining_date' => $validated['joining_date'],
                'status' => $validated['status'],
                'custom_monthly_amount' => $validated['custom_monthly_amount'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('admin.members.index')->with('success', "Member {$memberCode} added successfully!");
    }

    /**
     * Display the specified member with complete payment history and matrix.
     */
    public function show(Member $member)
    {
        $member->load(['user', 'payments.verifier']);

        $summary = $member->payment_summary;
        $dueMonths = $member->getDueMonthsList();

        // Key payments by YYYY-MM
        $paymentsByMonth = $member->payments->keyBy(function ($p) {
            return "{$p->payment_year}-{$p->payment_month}";
        });

        $currentYear = Carbon::now()->year;
        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('admin.members.show', compact('member', 'summary', 'dueMonths', 'paymentsByMonth', 'currentYear', 'availableYears'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/', Rule::unique('users', 'mobile')->ignore($member->user_id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($member->user_id)],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive,pending'],
            'custom_monthly_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($member, $validated) {
            $member->update([
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'] ?? $member->email,
                'address' => $validated['address'] ?? null,
                'joining_date' => $validated['joining_date'],
                'status' => $validated['status'],
                'custom_monthly_amount' => $validated['custom_monthly_amount'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($member->user) {
                $userUpdates = [
                    'name' => $validated['name'],
                    'mobile' => $validated['mobile'],
                    'email' => $validated['email'] ?? $member->user->email,
                    'status' => $validated['status'],
                ];

                if (!empty($validated['new_password'])) {
                    $userUpdates['password'] = Hash::make($validated['new_password']);
                }

                $member->user->update($userUpdates);
            }
        });

        return redirect()->route('admin.members.show', $member)->with('success', 'Member details updated successfully!');
    }

    /**
     * Approve a pending member registration.
     */
    public function approve(Member $member)
    {
        DB::transaction(function () use ($member) {
            $member->update(['status' => 'active']);
            if ($member->user) {
                $member->user->update(['status' => 'active']);
                
                \App\Models\CommitteeNotification::sendToUser(
                    $member->user->id,
                    'Membership Approved! 🙏',
                    "Your registration for {$member->name} (Member ID: {$member->member_code}) has been approved by Super Admin. You can now login and make monthly seva contributions.",
                    'general',
                    route('member.dashboard')
                );
            }
        });

        return back()->with('success', "Member {$member->name} ({$member->member_code}) approved and activated successfully!");
    }

    /**
     * Reject a pending member registration.
     */
    public function reject(Member $member)
    {
        DB::transaction(function () use ($member) {
            $member->update(['status' => 'inactive']);
            if ($member->user) {
                $member->user->update(['status' => 'inactive']);

                \App\Models\CommitteeNotification::sendToUser(
                    $member->user->id,
                    'Membership Application Update',
                    "Your membership application was not approved. Please contact the Mandir Seva Committee.",
                    'general',
                    route('login')
                );
            }
        });

        return back()->with('warning', "Member {$member->name} ({$member->member_code}) was rejected / set to inactive.");
    }

    /**
     * Toggle member active/inactive status.
     */
    public function toggleStatus(Member $member)
    {
        $newStatus = $member->status === 'active' ? 'inactive' : 'active';

        DB::transaction(function () use ($member, $newStatus) {
            $member->update(['status' => $newStatus]);
            if ($member->user) {
                $member->user->update(['status' => $newStatus]);
            }
        });

        return back()->with('success', "Member {$member->member_code} is now {$newStatus}.");
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member)
    {
        $code = $member->member_code;
        $name = $member->name;

        DB::transaction(function () use ($member) {
            // Delete member payments
            $member->payments()->delete();

            // Delete associated user account and notifications if exists
            if ($member->user) {
                $member->user->notifications()->delete();
                $member->user->delete();
            }

            $member->delete();
        });

        return redirect()->route('admin.members.index')->with('success', "Member {$name} ({$code}) deleted successfully.");
    }

    /**
     * Record a manual payment (Cash / Bank / Admin entry) for a member.
     */
    public function recordManualPayment(Request $request, Member $member)
    {
        $validated = $request->validate([
            'payment_month' => ['required', 'integer', 'between:1,12'],
            'payment_year' => ['required', 'integer', 'between:2020,2035'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,upi_qr,bank_transfer'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Check duplicate
        $existing = Payment::where('member_id', $member->id)
            ->where('payment_month', $validated['payment_month'])
            ->where('payment_year', $validated['payment_year'])
            ->whereIn('status', ['approved', 'pending'])
            ->first();

        if ($existing) {
            $monthName = Carbon::create($validated['payment_year'], $validated['payment_month'], 1)->format('F Y');
            return back()->withErrors(['payment_month' => "A payment for {$monthName} already exists for this member."]);
        }

        $receiptNumber = Payment::generateReceiptNumber();

        $payment = Payment::create([
            'member_id' => $member->id,
            'amount' => $validated['amount'],
            'payment_month' => $validated['payment_month'],
            'payment_year' => $validated['payment_year'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? ('MANUAL-' . strtoupper(substr(uniqid(), -6))),
            'receipt_number' => $receiptNumber,
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? 'Manual payment recorded by Admin',
        ]);

        return back()->with('success', "Payment of ₹{$payment->amount} recorded successfully. Receipt No: {$receiptNumber}");
    }
}
