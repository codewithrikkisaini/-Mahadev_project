@extends('layouts.admin')

@section('title', 'Pending Verifications')
@section('header_title', 'UPI / QR Verification Queue')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, modalImg: '', showRejectModal: false, rejectPaymentId: null, rejectMemberName: '', rejectMonth: '' }">
    <!-- Header Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-950/40 via-slate-900/80 to-rose-950/40 border border-amber-500/30 backdrop-blur-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center font-bold text-xl sacred-glow">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                    <span>Payment Verification Queue</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-600 text-white font-bold">{{ $pendingPayments->total() }} Pending</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Inspect payment screenshots, cross-verify UTR with bank/UPI statement, and approve or reject.</p>
            </div>
        </div>

        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition self-start sm:self-center">
            <i class="fa-solid fa-list"></i> All Payments Ledger
        </a>
    </div>

    <!-- Cards Grid / Table for Pending Verifications -->
    @if($pendingPayments->isEmpty())
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-16 text-center shadow-xl">
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-3xl mx-auto mb-4 sacred-glow">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="text-lg font-bold font-cinzel text-emerald-300">Queue is Clear!</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">There are no pending member payments waiting for verification right now.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendingPayments as $payment)
                <div class="rounded-2xl bg-slate-900/90 border border-amber-500/30 hover:border-amber-500/60 transition shadow-xl p-5 flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>

                    <div>
                        <!-- Header with Member details -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($payment->member->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-100 text-sm">{{ $payment->member->name }}</h4>
                                    <p class="font-mono text-[11px] text-amber-400">{{ $payment->member->member_code }} • {{ $payment->member->mobile }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Pending
                            </span>
                        </div>

                        <!-- Contribution Details -->
                        <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 space-y-2 mb-4 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Month & Year:</span>
                                <span class="font-bold text-slate-200">{{ $payment->formatted_month }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Contribution Amount:</span>
                                <span class="font-cinzel font-bold text-emerald-400 text-sm">₹{{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Payment Date:</span>
                                <span class="font-medium text-slate-300">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                                <span class="text-slate-400">Submitted UTR / Ref:</span>
                                <span class="font-mono font-bold text-amber-300">{{ $payment->transaction_id }}</span>
                            </div>
                        </div>

                        <!-- Screenshot Thumbnail Preview -->
                        <div class="mb-4">
                            <span class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Submitted Proof / Screenshot</span>
                            @if($payment->screenshot_path)
                                <button
                                    type="button"
                                    @click="modalImg = '{{ asset('storage/' . $payment->screenshot_path) }}'; modalOpen = true"
                                    class="w-full h-36 rounded-xl overflow-hidden bg-slate-950 border border-slate-700 hover:border-amber-500 transition relative group/img cursor-pointer"
                                >
                                    <img src="{{ asset('storage/' . $payment->screenshot_path) }}" alt="Screenshot" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center gap-2 text-white text-xs font-semibold">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Click to Zoom Full Image
                                    </div>
                                </button>
                            @else
                                <div class="w-full h-24 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-slate-600 text-xs">
                                    No Screenshot Attached
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approve & Reject Action Bar -->
                    <div class="pt-3 border-t border-slate-800 grid grid-cols-2 gap-2">
                        <!-- Reject Button -->
                        <button
                            type="button"
                            @click="rejectPaymentId = {{ $payment->id }}; rejectMemberName = '{{ $payment->member->name }}'; rejectMonth = '{{ $payment->formatted_month }}'; showRejectModal = true"
                            class="py-2 px-3 bg-rose-950/40 hover:bg-rose-900/60 border border-rose-500/30 text-rose-300 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5"
                        >
                            <i class="fa-solid fa-xmark"></i> Reject
                        </button>

                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full py-2 px-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-md transition flex items-center justify-center gap-1.5"
                            >
                                <i class="fa-solid fa-check"></i> Approve & Issue Receipt
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($pendingPayments->hasPages())
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                <div class="text-xs text-slate-400">
                    Showing {{ $pendingPayments->firstItem() }} to {{ $pendingPayments->lastItem() }} of {{ $pendingPayments->total() }} pending items
                </div>
                <div>{{ $pendingPayments->links() }}</div>
            </div>
        @endif
    @endif

    <!-- Screenshot Zoom Modal -->
    <div
        x-show="modalOpen"
        @click="modalOpen = false"
        x-transition
        class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="max-w-2xl w-full bg-slate-900 border border-slate-700 rounded-2xl p-4 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-slate-200">UPI Payment Screenshot Verification</h4>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="max-h-[75vh] overflow-auto rounded-xl bg-black flex items-center justify-center">
                <img :src="modalImg" alt="Screenshot Full" class="max-w-full max-h-[70vh] object-contain">
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div
        x-show="showRejectModal"
        class="fixed inset-0 z-50 bg-black/85 flex items-center justify-center p-4 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="w-full max-w-md bg-slate-900 border border-rose-500/40 rounded-2xl p-6 shadow-2xl relative" @click.outside="showRejectModal = false">
            <h4 class="text-base font-bold text-rose-400 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Reject Payment Submission</span>
            </h4>
            <p class="text-xs text-slate-400 mb-4">
                You are rejecting submission of <strong class="text-slate-200" x-text="rejectMemberName"></strong> for <span class="text-amber-400" x-text="rejectMonth"></span>.
            </p>

            <form :action="'/admin/payments/' + rejectPaymentId + '/reject'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="rejection_reason_field" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Specify Rejection Reason *
                    </label>
                    <textarea
                        id="rejection_reason_field"
                        name="rejection_reason"
                        required
                        rows="3"
                        placeholder="e.g. UTR number does not match mandir bank statement / Wrong screenshot uploaded"
                        class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-slate-100 text-xs focus:border-rose-500 outline-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-lg shadow-rose-950/50">
                        <i class="fa-solid fa-ban"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
