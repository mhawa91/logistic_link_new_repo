<?php

namespace Modules\ContentTypes\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\ContentTypes\Entities\Invoice;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\TransporterContractInstance;

class DashboardStatisticsService
{
    private const IN_PROGRESS_SHIPPMENT_STATUSES = [
        'PENDING_PICKUP',
        'PENDING_DELIVERY',
    ];

    private const DELIVERED_SHIPPMENT_STATUSES = [
        'DELIVERED_PENDING_PAYMENT',
        'INVOICED',
        'COMPLETED',
    ];

    private const OPEN_INVOICE_STATUSES = [
        'PENDING',
        'PENDING_SUPPLIER_REVIEW',
    ];

    public function forUser(User $user): array
    {
        $shippments = $this->shippmentsFor($user);
        $invoices = $this->invoicesFor($user);
        $contracts = $this->contractsFor($user);

        return [
            'total_contracts'      => (clone $contracts)->count(),
            'total_shippments'     => (clone $shippments)->count(),
            'delivered_shippments' => (clone $shippments)->whereIn('sys_status', self::DELIVERED_SHIPPMENT_STATUSES)->count(),
            'in_progress'          => (clone $shippments)->whereIn('sys_status', self::IN_PROGRESS_SHIPPMENT_STATUSES)->count(),
            'open_invoices'        => (clone $invoices)->whereIn('sys_status', self::OPEN_INVOICE_STATUSES)->count(),
            'chart'                => [
                'last_7_days'  => $this->dailyShippments((clone $shippments), 7),
                'last_30_days' => $this->dailyShippments((clone $shippments), 30),
            ],
        ];
    }

    private function shippmentsFor(User $user): Builder
    {
        return Shippment::query()
            ->when($user->type === 'DEFAULT', function (Builder $query) use ($user) {
                $query->where('company_user_id', $user->id);
            })
            ->when($user->type === 'TRANSPORTER', function (Builder $query) use ($user) {
                $query->where('transporter_user_id', $user->id);
            });
    }

    private function invoicesFor(User $user): Builder
    {
        return Invoice::query()
            ->when($user->type === 'DEFAULT', function (Builder $query) use ($user) {
                $query->where('company_user_id', $user->id);
            })
            ->when($user->type === 'TRANSPORTER', function (Builder $query) use ($user) {
                $query->where('transportar_user_id', $user->id);
            });
    }

    private function contractsFor(User $user): Builder
    {
        return TransporterContractInstance::query()
            ->when($user->type === 'DEFAULT', function (Builder $query) use ($user) {
                $query->where('company_user_id', $user->id);
            })
            ->when($user->type === 'TRANSPORTER', function (Builder $query) use ($user) {
                $query->where('transporter_user_id', $user->id);
            });
    }

    private function dailyShippments(Builder $query, int $days): array
    {
        $end = Carbon::today();
        $start = (clone $end)->subDays($days - 1);
        $counts = $query
            ->whereBetween('created_at', [(clone $start)->startOfDay(), (clone $end)->endOfDay()])
            ->get(['created_at'])
            ->countBy(function (Shippment $shippment) {
                return $shippment->created_at->format('Y-m-d');
            });

        $labels = [];
        $values = [];

        for ($date = clone $start; $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->locale(app()->getLocale())->translatedFormat('d M');
            $values[] = $counts->get($key, 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'total'  => array_sum($values),
        ];
    }
}
