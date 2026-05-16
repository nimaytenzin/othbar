<div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    @livewire(\App\Livewire\Admin\PaymentVerification::class, ['order' => $order], key('pv-'.$order->id))
</div>
