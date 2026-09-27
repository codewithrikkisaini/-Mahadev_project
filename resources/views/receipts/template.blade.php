<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Seva Receipt #{{ $payment->receipt_number }} | {{ $templeName }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700&family=Rozha+One&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        cinzel: ['Cinzel', 'serif'],
                        sacred: ['Rozha One', 'serif']
                    }
                }
            }
        }
    </script>

    <style>
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: 2px solid #000000 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 24px !important;
            }
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 260px;
            color: rgba(245, 158, 11, 0.05);
            font-family: 'Rozha One', serif;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-800 font-sans min-h-screen py-8 px-4 flex flex-col items-center justify-center selection:bg-amber-500 selection:text-white">
    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-2xl w-full mb-4 flex items-center justify-between">
        <button
            onclick="window.history.back()"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition border border-slate-700"
        >
            <i class="fa-solid fa-arrow-left"></i> Back
        </button>

        <div class="flex items-center gap-2">
            <button
                onclick="window.print()"
                class="px-5 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-orange-950/50 flex items-center gap-2 transition cursor-pointer"
            >
                <i class="fa-solid fa-print"></i> Print / Download PDF
            </button>
        </div>
    </div>

    <!-- Official Receipt Printable Container -->
    <div class="receipt-container max-w-2xl w-full bg-white rounded-2xl shadow-2xl p-6 sm:p-10 border-4 border-amber-600 relative overflow-hidden text-slate-900">
        <!-- Watermark -->
        <div class="watermark">
            ॐ
        </div>

        <!-- Decorative Outer Border -->
        <div class="border border-amber-400/60 p-5 rounded-xl relative z-10">
            <!-- Header -->
            <div class="text-center pb-5 border-b-2 border-amber-500/40 relative">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-600 font-sacred text-3xl font-bold mb-1">
                    ॐ
                </div>
                <div class="text-xs font-bold text-amber-700 uppercase tracking-widest">हर हर महादेव • सेवा ही संकल्प</div>
                <h1 class="text-2xl sm:text-3xl font-black font-cinzel text-slate-900 tracking-wide mt-1">
                    {{ $templeName }}
                </h1>
                <h2 class="text-sm font-semibold text-slate-700">
                    {{ $committeeName }}
                </h2>
                @if($committeeAddress)
                    <p class="text-[11px] text-slate-500 mt-1 max-w-md mx-auto">{{ $committeeAddress }}</p>
                @endif
                @if($contactNumber)
                    <p class="text-[11px] text-slate-500">Contact: {{ $contactNumber }}</p>
                @endif

                <div class="mt-3 inline-block px-4 py-1 rounded-full bg-amber-600 text-white font-cinzel font-bold text-xs uppercase tracking-widest shadow-sm">
                    Official Seva Contribution Receipt
                </div>
            </div>

            <!-- Receipt Meta Info Row -->
            <div class="grid grid-cols-2 gap-4 py-4 border-b border-slate-200 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Receipt Number</span>
                    <span class="font-mono font-black text-amber-700 text-sm sm:text-base">{{ $payment->receipt_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Payment / Issue Date</span>
                    <span class="font-bold text-slate-900">{{ $payment->payment_date ? $payment->payment_date->format('d F, Y') : date('d F, Y') }}</span>
                </div>
            </div>

            <!-- Member & Contribution Details Grid -->
            <div class="py-5 space-y-3.5 text-xs sm:text-sm">
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Received With Thanks From:</span>
                    <span class="font-bold text-slate-900 text-sm sm:text-base">{{ $payment->member->name }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Member ID / Code:</span>
                    <span class="font-mono font-bold text-amber-800">{{ $payment->member->member_code }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Contact Mobile:</span>
                    <span class="font-semibold text-slate-800">{{ $payment->member->mobile }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Contribution For Period:</span>
                    <span class="font-bold font-cinzel text-slate-900">{{ $payment->formatted_month }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Payment Mode & Reference:</span>
                    <span class="font-medium text-slate-800">
                        <span class="capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                        @if($payment->transaction_id)
                            • <span class="font-mono text-slate-600">{{ $payment->transaction_id }}</span>
                        @endif
                    </span>
                </div>

                <!-- Amount Box -->
                <div class="my-4 p-4 rounded-xl bg-amber-50 border-2 border-amber-300 flex items-center justify-between">
                    <div>
                        <span class="text-xs uppercase font-bold text-amber-900 block">Total Amount Received</span>
                        <span class="text-[11px] text-slate-600">Toward Monthly Mandir Seva & Maintenance Fund</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl sm:text-3xl font-black font-cinzel text-amber-700">
                            ₹{{ number_format($payment->amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Signatures & QR Authentication -->
            <div class="pt-6 mt-2 border-t border-slate-200 grid grid-cols-2 items-end">
                <!-- QR Verification Code -->
                <div class="flex items-center gap-3">
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&margin=2&data={{ urlencode(url()->current()) }}"
                        alt="Verification QR"
                        class="w-16 h-16 rounded border border-slate-300"
                    >
                    <div class="text-[10px] text-slate-500 leading-tight">
                        <strong class="text-slate-800 block">Verified Digital Receipt</strong>
                        <span>Scan QR code to verify validity on Mandir Seva portal.</span>
                    </div>
                </div>

                <!-- Authorized Signature -->
                <div class="text-right">
                    <div class="h-10"></div>
                    <div class="border-t border-slate-400 inline-block pt-1 min-w-[140px] text-center">
                        <p class="text-xs font-bold text-slate-900">Authorized Signatory</p>
                        <p class="text-[10px] text-slate-500 font-medium">{{ $committeeName }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
