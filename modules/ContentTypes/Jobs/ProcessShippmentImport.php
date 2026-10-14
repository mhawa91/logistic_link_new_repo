<?php

namespace Modules\ContentTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\ContentTypes\Entities\ShippmentImport;
use Modules\ContentTypes\Imports\ShippmentImportProcessor;

class ProcessShippmentImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1000;
    public $maxExceptions = 3;
    public $timeout = 85;
    public $failOnTimeout = true;
    public $uniqueFor = 86400;

    private $importId;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    public function uniqueId()
    {
        return (string) $this->importId;
    }

    public function handle(ShippmentImportProcessor $processor)
    {
        $lock = Cache::lock('shippment-import-processing:' . $this->importId, 90);
        if (! $lock->get()) {
            return;
        }

        try {
            $import = ShippmentImport::with('user')->findOrFail($this->importId);
            if (! $import->user || $import->user->type !== 'DEFAULT') {
                throw new \RuntimeException('Shipment imports require a DEFAULT client account.');
            }
            Auth::onceUsingId($import->user_id);
            $import->update(['status' => 'PROCESSING', 'started_at' => $import->started_at ?: now(), 'error_message' => null]);

            try {
                $completed = $processor->process($import->fresh('user'));
                if (! $completed) {
                    $this->release(5);
                }
            } catch (\Throwable $exception) {
                report($exception);
                $import->update([
                    'status' => 'FAILED',
                    'error_message' => $exception instanceof \Illuminate\Validation\ValidationException
                        ? collect($exception->errors())->flatten()->implode(' ')
                        : __('contenttypes::strings.shipment_import.errors.import_failed'),
                    'completed_at' => now(),
                ]);
            }
        } finally {
            optional($lock)->release();
        }
    }

    public function failed(\Throwable $exception): void
    {
        $import = ShippmentImport::find($this->importId);
        if (! $import) {
            return;
        }
        app()->setLocale(in_array($import->locale, ['ar', 'en']) ? $import->locale : 'en');
        $import->update([
            'status' => 'FAILED',
            'error_message' => __('contenttypes::strings.shipment_import.errors.import_failed'),
            'completed_at' => now(),
        ]);
    }
}
