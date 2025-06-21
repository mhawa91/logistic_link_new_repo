<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use Hash;
use Bouncer;
use DataTables;
use Modules\SystemCore\System\Services\ModelService;
use Modules\Admin\Entities\UserService;

class ClientSelectService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = config('systemcore.models.User');
        $this->guard();
        $this->registerAjaxSelectCallback(function($Row) {
            $name = ! $Row ? '-- Select --' : ($Row->name ? ucwords($Row->name) : ($Row->first_name ? ucwords($Row->first_name . ' ' . $Row->last_name) : 'N/A')) . ' | ' . ($Row->phone_number ?? $Row->email ?? $Row->username);
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => $name,
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function can(string $permission)
    {
        return true;
    }

    public function create(array $data = [])
    {
        return null;
    }

    public function update(array $data = [])
    {
        return null;
    }

    public function updateProfile(array $data = [])
    {
        return null;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        if (! empty($Options['has_uninvoiced_invoices'])) {
            $this->model()->where(function($Model) use($Options) {
                $Model->whereHas('shippmentsAsClient', function($rel) use($Options) {
                    $rel->where('sys_status', 'DELIVERED_PENDING_PAYMENT');
                    if (! empty($Options['transportar_id'])) {
                        $rel->where('transporter_user_id', $Options['transportar_id']);
                    }
                });
            });
        }
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function dataTable($Filter = null)
    {
        $this->model()->select([
            'users.*',
        ])
        ->WithMetaSelect()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->WithClientInvoicesFinanceSelect();
            $query->WithClientShippmentsFinanceSelect();
            $query->whereHas('shippmentsAsClient', function($rel) {
                $rel->where('company_user_id', auth()->id());
            });
            $query->groupBy('invoices.transportar_user_id');
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->WithTransportarInvoicesFinanceSelect();
            $query->WithTransportarShippmentsFinanceSelect();
            $query->whereHas('shippmentsAsTransportar', function($rel) {
                $rel->where('transporter_user_id', auth()->id());
            });
            $query->groupBy('invoices.company_user_id');
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query->groupBy('users.id');
        });

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        $table->addColumn('formated_invoices_dues_count', function($row) use($helper) {
            return $helper->moneyFormat($row->invoices_dues_count, 0)->abs;
        });
        $table->addColumn('formated_invoices_dues_total', function($row) use($helper) {
            return $helper->moneyFormat($row->invoices_dues_total, 2)->abs;
        });
        $table->addColumn('formated_shippments_uninvoiced_count', function($row) use($helper) {
            return $helper->moneyFormat($row->shippments_uninvoiced_count, 0)->abs;
        });
        $table->addColumn('formated_shippments_uninvoiced_total', function($row) use($helper) {
            return $helper->moneyFormat($row->shippments_uninvoiced_total, 2)->abs;
        });
        $table->addColumn('formated_shippments_invoiced_count', function($row) use($helper) {
            return $helper->moneyFormat($row->shippments_invoiced_count, 0)->abs;
        });
        $table->addColumn('formated_shippments_invoiced_total', function($row) use($helper) {
            return $helper->moneyFormat($row->shippments_invoiced_total, 2)->abs;
        });

        return $table;
    }
}
