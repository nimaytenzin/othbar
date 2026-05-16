<div class="divide-y divide-gray-200 dark:divide-white/10">

    {{-- Section header --}}
    <div class="py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs leading-4 font-medium tracking-wider text-gray-900 uppercase dark:text-white">
                Payment Verification
            </h3>
            <x-filament::badge
                size="sm"
                :color="$order->payment_status->getColor()"
                :icon="$order->payment_status->getIcon()"
            >
                {{ $order->payment_status->getLabel() }}
            </x-filament::badge>
        </div>

        {{-- Flash messages --}}
        @if(session('payment_verified'))
        <div class="mt-3 flex items-center gap-2 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-3 py-2 text-xs text-green-800 dark:text-green-300">
            <svg class="size-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('payment_verified') }}
        </div>
        @endif
        @if(session('payment_rejected'))
        <div class="mt-3 flex items-center gap-2 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-3 py-2 text-xs text-red-800 dark:text-red-300">
            <svg class="size-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
            {{ session('payment_rejected') }}
        </div>
        @endif
    </div>

    {{-- Payment details --}}
    @php
        $meta          = is_array($order->metadata) ? $order->metadata : (json_decode((string) $order->metadata, true) ?: []);
        $customerEmail = $meta['email'] ?? null;
        $couponCode    = $meta['coupon_code'] ?? null;
    @endphp

    <div class="space-y-3 py-4">

        <div class="flex items-start justify-between text-sm">
            <span class="text-gray-500 dark:text-gray-400">Method</span>
            <span class="font-medium text-gray-900 dark:text-white text-right">Scan to Pay<br><span class="text-xs font-normal text-gray-500 dark:text-gray-400">Bank Transfer</span></span>
        </div>

        @if($order->payment_reference)
        <div class="flex items-start justify-between text-sm gap-3">
            <span class="text-gray-500 dark:text-gray-400 shrink-0">Reference no.</span>
            <code class="font-mono text-xs font-semibold text-gray-900 dark:text-white text-right break-all">{{ $order->payment_reference }}</code>
        </div>
        @endif

        @if($customerEmail)
        <div class="flex items-start justify-between text-sm gap-3">
            <span class="text-gray-500 dark:text-gray-400 shrink-0">Email</span>
            <a href="mailto:{{ $customerEmail }}" class="text-primary-600 hover:text-primary-500 underline text-xs text-right break-all">{{ $customerEmail }}</a>
        </div>
        @endif

        @if($couponCode)
        <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 dark:text-gray-400">Coupon</span>
            <code class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono">{{ $couponCode }}</code>
        </div>
        @endif

    </div>

    {{-- Payment screenshot --}}
    <div class="py-4">
        <h3 class="text-xs leading-4 font-medium tracking-wider text-gray-900 uppercase dark:text-white mb-3">
            Payment Screenshot
        </h3>

        @if($order->payment_proof_path)

            @if($proofIsImage)
            <div class="relative group overflow-hidden rounded-lg border border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-gray-800 cursor-pointer"
                 onclick="document.getElementById('proof-modal-{{ $order->id }}').classList.remove('hidden')">
                <img src="{{ $proofUrl }}"
                     alt="Payment proof"
                     class="w-full max-h-36 object-contain"
                     onerror="this.closest('div').innerHTML='<p class=\'p-3 text-xs text-gray-500 dark:text-gray-400\'>Could not load image.</p>'">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-xs font-medium px-2.5 py-1 rounded-full shadow">
                        View full size
                    </span>
                </div>
            </div>

            {{-- Lightbox --}}
            <div id="proof-modal-{{ $order->id }}"
                 class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                 onclick="this.classList.add('hidden')">
                <div class="relative max-w-3xl max-h-[90vh] w-full">
                    <img src="{{ $proofUrl }}" alt="Payment proof" class="w-full h-full object-contain rounded-lg shadow-2xl">
                    <button class="absolute top-2 right-2 bg-white/20 hover:bg-white/40 text-white rounded-full p-1.5 transition-colors"
                            onclick="event.stopPropagation(); document.getElementById('proof-modal-{{ $order->id }}').classList.add('hidden')">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            @else
            {{-- PDF --}}
            <div class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800/50 p-3">
                <div class="flex size-8 items-center justify-center rounded-md bg-red-50 dark:bg-red-900/20 shrink-0 ring-1 ring-red-200 dark:ring-red-800">
                    <svg class="size-4 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ basename($order->payment_proof_path) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF document</p>
                </div>
                <a href="{{ $proofUrl }}" target="_blank"
                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 shrink-0">
                    Open
                </a>
            </div>
            @endif

            <div class="mt-2">
                <a href="{{ $proofUrl }}" download
                   class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download proof
                </a>
            </div>

        @else
        <div class="inline-flex items-center gap-2 rounded-sm bg-gray-50 px-4 py-2 dark:bg-gray-800">
            <svg class="size-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span class="text-sm text-gray-500 dark:text-gray-400">No screenshot uploaded</span>
        </div>
        @endif
    </div>

    {{-- Verify actions --}}
    <div class="py-4">
        @if($order->payment_status === \App\Enums\PaymentStatus::Pending)

            @if(!$showConfirmApprove && !$showConfirmReject)
            <div class="grid grid-cols-2 gap-2">
                @if($order->payment_proof_path)
                <button wire:click="$set('showConfirmApprove', true)"
                        style="background:#16a34a;color:#fff;border:none;"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-opacity hover:opacity-90">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Approve
                </button>
                @else
                <div class="col-span-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
                    Approve is unavailable until the customer uploads a payment screenshot.
                </div>
                @endif
                <button wire:click="$set('showConfirmReject', true)"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:border-white/10 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors {{ ! $order->payment_proof_path ? 'col-span-2' : '' }}">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject
                </button>
            </div>
            @endif

            @if($showConfirmApprove)
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 space-y-3">
                <div>
                    <p class="text-sm font-semibold text-green-900 dark:text-green-200">Approve this payment?</p>
                    <p class="text-xs text-green-700 dark:text-green-400 mt-0.5">Sets payment to <strong>Paid</strong> and order to <strong>Processing</strong>.</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="approve" wire:loading.attr="disabled"
                            style="background:#16a34a;color:#fff;border:none;"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition-opacity hover:opacity-90 disabled:opacity-50">
                        <span wire:loading.remove wire:target="approve">Yes, approve</span>
                        <span wire:loading wire:target="approve">Approving…</span>
                    </button>
                    <button wire:click="$set('showConfirmApprove', false)"
                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
            @endif

            @if($showConfirmReject)
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 space-y-3">
                <div>
                    <p class="text-sm font-semibold text-red-900 dark:text-red-200">Reject this payment?</p>
                    <p class="text-xs text-red-700 dark:text-red-400 mt-0.5">Sets payment to <strong>Voided</strong> and cancels the order.</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="reject" wire:loading.attr="disabled"
                            style="background:#dc2626;color:#fff;border:none;"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition-opacity hover:opacity-90 disabled:opacity-50">
                        <span wire:loading.remove wire:target="reject">Yes, reject</span>
                        <span wire:loading wire:target="reject">Rejecting…</span>
                    </button>
                    <button wire:click="$set('showConfirmReject', false)"
                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
            @endif

        @elseif($order->payment_status === \App\Enums\PaymentStatus::Paid)
        <div class="inline-flex items-center gap-2 rounded-sm bg-green-50 dark:bg-green-900/20 px-3 py-2">
            <svg class="size-4 text-green-500 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-green-700 dark:text-green-400">Payment verified and approved.</span>
        </div>

        @elseif($order->payment_status === \App\Enums\PaymentStatus::Voided)
        <div class="inline-flex items-center gap-2 rounded-sm bg-red-50 dark:bg-red-900/20 px-3 py-2">
            <svg class="size-4 text-red-500 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-red-700 dark:text-red-400">Payment rejected. Order cancelled.</span>
        </div>
        @endif
    </div>

</div>
