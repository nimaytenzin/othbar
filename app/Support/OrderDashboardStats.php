<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Carbon;

class OrderDashboardStats
{
    public function monthStart(): Carbon
    {
        return today()->startOfMonth();
    }

    public function monthSalesRevenueMinor(): int
    {
        return (int) Order::query()
            ->successful()
            ->where('created_at', '>=', $this->monthStart())
            ->sum('total_minor');
    }

    public function monthSalesCount(): int
    {
        return Order::query()
            ->successful()
            ->where('created_at', '>=', $this->monthStart())
            ->count();
    }

    public function previousMonthSalesRevenueMinor(): int
    {
        $start = $this->monthStart()->copy()->subMonth();
        $end = $this->monthStart()->copy()->subSecond();

        return (int) Order::query()
            ->successful()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_minor');
    }

    public function todayOrdersCount(): int
    {
        return Order::query()
            ->whereDate('created_at', today())
            ->where('status', '!=', OrderStatus::Cancelled)
            ->count();
    }

    public function pendingPaymentCount(): int
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Pending)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->count();
    }

    public function todayPendingPaymentCount(): int
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Pending)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->whereDate('created_at', today())
            ->count();
    }

    public function awaitingFulfillmentCount(): int
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->whereNotIn('status', [OrderStatus::Completed, OrderStatus::Cancelled])
            ->count();
    }

    public function fulfilledThisMonthCount(): int
    {
        return Order::query()
            ->where('status', OrderStatus::Completed)
            ->where('updated_at', '>=', $this->monthStart())
            ->count();
    }

    public function totalActiveOrdersCount(): int
    {
        return Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->count();
    }

    public function paidOrdersCount(): int
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->count();
    }

    /**
     * @return array{labels: list<string>, placed: list<int>, fulfilled: list<int>}
     */
    public function dailyPlacedVsFulfilled(int $days = 30): array
    {
        $days = max(1, $days);
        $start = today()->subDays($days - 1);

        $placedByDay = Order::query()
            ->where('created_at', '>=', $start)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day');

        $fulfilledByDay = Order::query()
            ->where('status', OrderStatus::Completed)
            ->where('updated_at', '>=', $start)
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day');

        $labels = [];
        $placed = [];
        $fulfilled = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('M j');
            $placed[] = (int) ($placedByDay[$key] ?? 0);
            $fulfilled[] = (int) ($fulfilledByDay[$key] ?? 0);
        }

        return compact('labels', 'placed', 'fulfilled');
    }

    /**
     * @return list<float>
     */
    public function lastSevenDayRevenueSparkline(): array
    {
        $start = today()->subDays(6);

        $byDay = Order::query()
            ->successful()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, SUM(total_minor) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $values = [];

        for ($i = 0; $i < 7; $i++) {
            $key = $start->copy()->addDays($i)->toDateString();
            $values[] = ((int) ($byDay[$key] ?? 0)) / 100;
        }

        return $values;
    }

    public function monthSalesTrendDescription(): string
    {
        $previous = $this->previousMonthSalesRevenueMinor();
        $current = $this->monthSalesRevenueMinor();
        $count = $this->monthSalesCount();

        if ($previous === 0) {
            return $count.' fulfilled '.str('order')->plural($count).' this month';
        }

        $change = (int) round((($current - $previous) / $previous) * 100);
        $trend = $change >= 0 ? "+{$change}%" : "{$change}%";

        return "{$trend} vs last month · {$count} orders";
    }

    public function pendingPaymentDescription(): string
    {
        $today = $this->todayPendingPaymentCount();
        $total = $this->pendingPaymentCount();

        if ($total === 0) {
            return 'No orders awaiting payment';
        }

        if ($today === 0) {
            return "{$total} awaiting payment confirmation";
        }

        return "{$today} placed today · {$total} total pending";
    }

    public static function formatMoney(int $minor): string
    {
        return 'Nu. '.number_format($minor / 100, $minor % 100 === 0 ? 0 : 2);
    }
}
