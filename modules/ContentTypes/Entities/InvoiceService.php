<?php

namespace Modules\ContentTypes\Entities;

use Validator;
use DB;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;
use Modules\SystemCore\System\Services\ExportService;
use Carbon\Carbon;
use App\Models\User;

class InvoiceService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Invoice::class;
        $this->guard();
        // if ($this->can('read')) {
        //     $this->registerDataTableAction('read', function($Row) {
        //         return [
        //             'label'  => __('ui::strings.read'),
        //             'action' => 'javascript:;',
        //         ];
        //     });
        // }
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('InvoicesController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('InvoicesController@postUpdate', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('InvoicesController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('InvoicesController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        }
        $this->registerAjaxSelectCallback(function($Row) {
            return [
                'id'   => ! $Row ? '0'            : $Row->id,
                'text' => ! $Row ? '-- Select --' : $Row->code . ' - [' . $Row->symbol . ']',
                'data' => ! $Row ? []             : $Row->toArray(),
            ];
        });
    }

    public function validator($type = 'create', array $data = [])
    {
        $rules = [
            'invoice_number'       => 'required|string|max:255',
            'client_id'            => 'required|exists:users,id',
            'note'                 => 'nullable|string|max:1000',
            'invoiced_at'          => 'required|date_format:Y-m-d',
            'due_at'               => 'required|date_format:Y-m-d|after_or_equal:invoiced_at',
            'items'                => 'required|array',
            'items.*.shippment_id' => 'required|exists:lgl_shippments,id,transporter_user_id,' . auth()->id(),
            'items.*.discount'     => 'nullable|numeric|min:0',
        ];
        if (auth()->user()->isAn('DEFAULT')) {
            $rules = [
                'ATTACHMENT' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];
        }
        
        if ($type == 'create') {
            return Validator::make($data, $rules);
        }
        if ($type == 'update') {
            if (auth()->user()->isAn('TRANSPORTER') && $this->data->sys_status == 'PENDING_SUPPLIER_REVIEW') {
                $rules = [];
            }
            return Validator::make($data, $rules);
        }
    }

    protected function keywordFilter($query, string $keyword = null)
    {
        $query->where(function($Model) use($keyword) {
            
        });
    }

    protected function advancedFilter($query, array $filter = [])
    {
        if (! empty($filter['title'])) {
            
        }
    }

    public function dataTable($Filter = null)
    {
        $this->model()->select([
            'lgl_invoices.*',
        ])
        ->withSelectMeta()
        ->when(auth()->check() && auth()->user()->type == 'DEFAULT', function($query) {
            $query->withTransportarSelect();
            $query->where('lgl_invoices.company_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type == 'TRANSPORTER', function($query) {
            $query->withClientSelect();
            $query->where('lgl_invoices.transportar_user_id', auth()->id());
        })
        ->when(auth()->check() && auth()->user()->type != 'DEFAULT' && auth()->user()->type != 'TRANSPORTER', function($query) {
            $query;
        });

        $helper = app(\Modules\SystemCore\System\Classes\Helpers::class);
        $table  = parent::dataTable($Filter);

        $table->addColumn('formated_sub_total', function($row) use($helper) {
            return $helper->moneyFormat($row->sub_total, 0)->abs;
        });
        $table->addColumn('formated_discounts_total', function($row) use($helper) {
            return $helper->moneyFormat($row->discounts_total, 2)->abs;
        });
        $table->addColumn('formated_total', function($row) use($helper) {
            return $helper->moneyFormat($row->total, 2)->abs;
        });

        return $table;
    }

    public function export($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $query    = $this->model();
        $this->filter($query, $filter);

        $exporter = app(ExportService::class)->of($query)->name('Data.csv');

        $exporter->addColumn('id', function($row) {
            return $row->id;
        });

        return $exporter;
    }

    public function create(array $data = [])
    {
        $transportar = User::with('company')->findOrFail(auth()->id());
        $client      = User::with('company')->findOrFail($data['client_id']);
        $ModelData = [
            'invoice_number'      => $data['invoice_number'] ?? null,
            'company_user_id'     => $client->id,
            'company_id'          => $client->company->id ?? null,
            'transportar_user_id' => $transportar->id,
            'transportar_id'      => $transportar->company->id ?? null,
            'note'                => $data['note'] ?? null,
            'invoiced_at'         => Carbon::parse($data['invoiced_at'])->format('Y-m-d 00:00:00'),
            'due_at'              => Carbon::parse($data['due_at'])->format('Y-m-d 00:00:00'),
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => null,
            'sys_status'         => $data['sys_status']  ?? 'PENDING',
        ];
        $model = parent::create( $ModelData )->data();

        $shippments = Shippment::whereIn('id', collect($data['items'] ?? [['shippment_id' => 0]])->pluck('shippment_id')->toArray())
        ->where('transporter_user_id', auth()->id())
        ->where('sys_status', 'DELIVERED_PENDING_PAYMENT')
        ->get();

        $items = collect($data['items'] ?? [])->map(function($item) use($model, $shippments, $data) {
            $shippment = $shippments->where('id', $item['shippment_id'] ?? null)->first();
            return [
                'invoice_id'   => $model->id,
                'shippment_id' => $shippment->id          ?? null,
                'description'  => $data['note']           ?? null,
                'sub_cost'     => $shippment->total_price ?? 0,
                'discount'     => $item['discount']       ?? 0,
                'cost'         => ($shippment->total_price - $item['discount'] < 0) ? 0 : $shippment->total_price - $item['discount'],
            ];
        });

        if (count($items) > 0) {
            $model->items()->insert($items->toArray());
        }

        $model->sub_total       = $items->sum('sub_cost');
        $model->discounts_total = $items->sum('discount');
        $model->total           = $items->sum('cost');
        $model->save();

        $shippments->each(function($shippment) use($model) {
            $shippment->sys_status = 'INVOICED';
            $shippment->invoice_id = $model->id;
            $shippment->save();
        });

        return $model;
    }

    public function syncFile(Invoice $invoice, string $type, $uplaodedFile)
    {
        if ($uplaodedFile && !is_string($uplaodedFile)) {
            $file = UploadedFile::firstOrNew([
                'file_tag'     => 'invoice_file',
                'related_type' => 'invoice',
                'related_id'   => $invoice->id,
            ]);
            $file->related_id         = $invoice->id;
            $file->related_type       = 'invoice';
            $file->file_tag           = 'invoice_file';
            $file->file_name          = $uplaodedFile->getClientOriginalName();
            $file->file_extension     = $uplaodedFile->getClientOriginalExtension();
            $file->file_size          = $uplaodedFile->getSize();
            $file->created_by_user_id = auth()->id();
            $file->updated_by_user_id = auth()->id();
            $file->sys_status         = 'ACTIVE';
            $file->file_path          = app('GraphManager')->process('files', $uplaodedFile);
            $file->save();
        }
    }

    public function update(array $data = [])
    {
        if (auth()->user()->isAn('DEFAULT') && $this->data->sys_status == 'PENDING') {
            $ModelData = [
                'paid_at'            => Carbon::now()->format('Y-m-d 00:00:00'),
                'updated_by_user_id' => auth()->id(),
                'sys_status'         => 'PENDING_SUPPLIER_REVIEW',
            ];
            $model = parent::update( $ModelData )->data();
            $this->syncFile($model, 'invoice_file', $data['ATTACHMENT'] ?? null);
            return $model;
        }
        else if (auth()->user()->isAn('DEFAULT')) {
            return $this->data;
        }

        if (auth()->user()->isAn('TRANSPORTER') && $this->data->sys_status == 'PENDING_SUPPLIER_REVIEW') {
            $ModelData = [
                'paid_at'            => Carbon::now()->format('Y-m-d 00:00:00'),
                'updated_by_user_id' => auth()->id(),
                'sys_status'         => 'PAID',
            ];
            $model = parent::update( $ModelData )->data();
            $shippments = Shippment::whereIn('id', $model->items()->pluck('shippment_id')->toArray())->get();
            $shippments->each(function($shippment) use($model) {
                $shippment->sys_status = 'COMPLETED';
                $shippment->invoice_id = $model->id;
                $shippment->save();
            });
            return $model;
        }

        $transportar = User::with('company')->findOrFail(auth()->id());
        $client      = User::with('company')->findOrFail($data['client_id']);
        $ModelData = [
            'invoice_number'      => $data['invoice_number'] ?? null,
            'company_user_id'     => $client->id,
            'company_id'          => $client->company->id ?? null,
            'transportar_user_id' => $transportar->id,
            'transportar_id'      => $transportar->company->id ?? null,
            'note'                => $data['note'] ?? null,
            'invoiced_at'         => Carbon::parse($data['invoiced_at'])->format('Y-m-d 00:00:00'),
            'due_at'              => Carbon::parse($data['due_at'])->format('Y-m-d 00:00:00'),
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => null,
            'sys_status'         => $data['sys_status']  ?? 'PENDING',
        ];
        $model = parent::update( $ModelData )->data();

        $model->items()->delete();

        $shippments = Shippment::whereIn('id', collect($data['items'] ?? [['shippment_id' => 0]])->pluck('shippment_id')->toArray())
        ->where('transporter_user_id', auth()->id())
        ->get();

        $items = collect($data['items'] ?? [])->map(function($item) use($model, $shippments, $data) {
            $shippment = $shippments->where('id', $item['shippment_id'] ?? null)->first();
            return [
                'invoice_id'   => $model->id,
                'shippment_id' => $shippment->id          ?? null,
                'description'  => $data['note']           ?? null,
                'sub_cost'     => $shippment->total_price ?? 0,
                'discount'     => $item['discount']       ?? 0,
                'cost'         => ($shippment->total_price - $item['discount'] < 0) ? 0 : $shippment->total_price - $item['discount'],
            ];
        });

        if (count($items) > 0) {
            $model->items()->insert($items->toArray());
        }

        $model->sub_total       = $items->sum('sub_cost');
        $model->discounts_total = $items->sum('discount');
        $model->total           = $items->sum('cost');
        $model->save();

        $shippments->each(function($shippment) use($model) {
            $shippment->sys_status = 'INVOICED';
            $shippment->invoice_id = $model->id;
            $shippment->save();
        });

        return $model;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        // $this->model()->whereNull('id');
        // $this->model()->with([
        //    'translations' => fn($rel) => $rel,
        // ]);
        return parent::ajaxSelect($filter, $Options, $page, $limit);
    }

    public function install() : bool
    {
        $Group = AbilityGroup::firstOrNew([
            'code' => $this->getPermissionGroupName()
        ]);
        $Group->icon               = null;
        $Group->{'default:ar'}      = 'Y';
        $Group->{'title:ar'}       = 'Invoice';
        $Group->{'description:ar'} = null;
        $Group->save();

        $read = Ability::firstOrNew([
            'name' => $this->getPermissionName('read')
        ]);
        $read->group_id           = $Group->id;
        $read->{'default:ar'}      = 'Y';
        $read->{'title:ar'}       = 'read';
        $read->{'description:ar'} = null;
        $read->save();

        $create = Ability::firstOrNew([
            'name' => $this->getPermissionName('create')
        ]);
        $create->group_id           = $Group->id;
        $create->{'default:ar'}      = 'Y';
        $create->{'title:ar'}       = 'create';
        $create->{'description:ar'} = null;
        $create->save();

        $update = Ability::firstOrNew([
            'name' => $this->getPermissionName('update')
        ]);
        $update->group_id           = $Group->id;
        $update->{'default:ar'}      = 'Y';
        $update->{'title:ar'}       = 'update';
        $update->{'description:ar'} = null;
        $update->save();

        $delete = Ability::firstOrNew([
            'name' => $this->getPermissionName('delete')
        ]);
        $delete->group_id           = $Group->id;
        $delete->{'default:ar'}      = 'Y';
        $delete->{'title:ar'}       = 'delete';
        $delete->{'description:ar'} = null;
        $delete->save();

        $restore = Ability::firstOrNew([
            'name' => $this->getPermissionName('restore')
        ]);
        $restore->group_id           = $Group->id;
        $restore->{'default:ar'}      = 'Y';
        $restore->{'title:ar'}       = 'restore';
        $restore->{'description:ar'} = null;
        $restore->save();

        $permaDelete = Ability::firstOrNew([
            'name' => $this->getPermissionName('permaDelete')
        ]);
        $permaDelete->group_id           = $Group->id;
        $permaDelete->{'default:ar'}      = 'Y';
        $permaDelete->{'title:ar'}       = 'permaDelete';
        $permaDelete->{'description:ar'} = null;
        $permaDelete->save();

        $statusUpdate = Ability::firstOrNew([
            'name' => $this->getPermissionName('statusUpdate')
        ]);
        $statusUpdate->group_id           = $Group->id;
        $statusUpdate->{'default:ar'}      = 'Y';
        $statusUpdate->{'title:ar'}       = 'statusUpdate';
        $statusUpdate->{'description:ar'} = null;
        $statusUpdate->save();

        $ADMIN_Role = Role::where('name', 'SYSTEM_ADMIN')->first();
        if ($ADMIN_Role) {
            $ADMIN_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        $CLIENT_Role = Role::where('name', 'DEFAULT')->first();
        if ($CLIENT_Role) {
            $CLIENT_Role->abilities()->syncWithoutDetaching([
                $read->id,
                // $create->id,
                $update->id,
                // $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        $TRANSPORTER_Role = Role::where('name', 'TRANSPORTER')->first();
        if ($TRANSPORTER_Role) {
            $TRANSPORTER_Role->abilities()->syncWithoutDetaching([
                $read->id,
                $create->id,
                $update->id,
                // $delete->id,
                // $permaDelete->id,
                $statusUpdate->id,
            ]);
        }

        return true;
    }
}
