<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class BackfillOrderInvoices extends Command
{
    protected $signature = 'invoices:backfill-orders {--dry-run : List orders without creating invoices}';

    protected $description = 'Create tax invoices for completed orders that do not have one yet';

    public function handle(InvoiceService $invoices): int
    {
        $query = Order::query()
            ->where('status', OrderStatus::Completed)
            ->whereNull('invoice_id');

        $count = $query->count();

        if ($count === 0) {
            $this->info('No completed orders need invoicing.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Would create {$count} invoice(s).");

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->with(['items.product', 'shippingAddress'])->chunkById(50, function ($orders) use ($invoices, $bar): void {
            foreach ($orders as $order) {
                $invoice = $invoices->createFromOrder($order);
                $order->update(['invoice_id' => $invoice->id]);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Created invoices for {$count} order(s).");

        return self::SUCCESS;
    }
}
