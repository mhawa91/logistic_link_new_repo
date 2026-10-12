@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.shipment_import.report_title', ['id' => $import->id]),
        'items' => [
            ['title' => __('contenttypes::strings.shipment_import.import_history'), 'link' => route('ShippmentsController@imports')],
            ['title' => '#' . $import->id, 'link' => route('ShippmentsController@readImport', ['import' => $import->id])],
        ],
    ]
])
@endcomponent

@include('contenttypes::shippments.imports._styles')

@section('content')
<div class="container-fluid mt-4" id="shipment-import-monitor"
     data-status="{{ $import->status }}"
     data-status-url="{{ route('ShippmentsController@importStatus', ['import' => $import->id]) }}">
    @if(in_array($import->status, ['PENDING', 'PROCESSING']))
        <div class="alert alert-warning d-flex align-items-center">
            <span class="spinner-border spinner-border-sm mr-2"></span>
            {{ __('contenttypes::strings.shipment_import.processing_notice') }}
        </div>
    @endif
    @if($import->error_message)<div class="alert alert-danger">{{ $import->error_message }}</div>@endif

    <div class="card shipment-import-card mb-4">
        <div class="shipment-import-summary">
            <div class="summary-main"><span class="summary-label">{{ __('contenttypes::strings.shipment_import.file') }}</span><span class="summary-value">{{ $import->original_filename }}</span></div>
            <div><span class="summary-label">{{ __('contenttypes::strings.shipment_import.status') }}</span><span class="badge badge-{{ $import->status === 'COMPLETED' ? 'success' : (in_array($import->status, ['FAILED', 'COMPLETED_WITH_ERRORS']) ? 'danger' : 'warning') }}">{{ __('contenttypes::strings.shipment_import.statuses.' . $import->status) }}</span></div>
            <div><span class="summary-label">{{ __('contenttypes::strings.shipment_import.created') }}</span><span class="summary-value summary-number text-success" id="shipment-import-created-count">{{ $import->created_count }}</span></div>
            <div><span class="summary-label">{{ __('contenttypes::strings.shipment_import.updated') }}</span><span class="summary-value summary-number text-primary" id="shipment-import-updated-count">{{ $import->updated_count }}</span></div>
            <div><span class="summary-label">{{ __('contenttypes::strings.shipment_import.failed') }}</span><span class="summary-value summary-number text-danger" id="shipment-import-failed-count">{{ $import->failed_count }}</span></div>
        </div>
        @if($import->total_rows > 0 || in_array($import->status, ['PENDING', 'PROCESSING']))
            <div class="shipment-import-progress {{ $import->total_rows > 0 ? '' : 'd-none' }}" id="shipment-import-progress">
                <div class="d-flex justify-content-between text-soft small mb-2">
                    <span>{{ __('contenttypes::strings.shipment_import.progress') }}</span>
                    <span><span id="shipment-import-processed-rows">{{ $import->processed_rows }}</span>/<span id="shipment-import-total-rows">{{ $import->total_rows }}</span></span>
                </div>
                <div class="progress"><div class="progress-bar" id="shipment-import-progress-bar" style="width: {{ $import->total_rows > 0 ? min(100, round(($import->processed_rows / $import->total_rows) * 100)) : 0 }}%"></div></div>
            </div>
        @endif
    </div>

    <div class="card shipment-import-card shipment-import-report">
        <div class="table-responsive">
            <table class="table table-middle mb-0">
                <thead><tr><th>{{ __('contenttypes::strings.shipment_import.row') }}</th><th>{{ __('contenttypes::strings.shipment_import.import_key') }}</th><th>{{ __('contenttypes::strings.shipment_import.operation') }}</th><th>{{ __('contenttypes::strings.shipment_import.status') }}</th><th>{{ __('contenttypes::strings.shipment_import.shipment') }}</th><th>{{ __('contenttypes::strings.shipment_import.failure_reasons') }}</th></tr></thead>
                <tbody>
                @forelse($import->rows as $row)
                    <tr>
                        <td>{{ $row->row_number }}</td><td>{{ $row->import_key ?: '—' }}</td>
                        <td>{{ __('contenttypes::strings.shipment_import.operations.' . $row->operation) }}</td>
                        <td><span class="badge badge-{{ $row->status === 'SUCCESS' ? 'success' : ($row->status === 'FAILED' ? 'danger' : 'warning') }}">{{ __('contenttypes::strings.shipment_import.row_statuses.' . $row->status) }}</span></td>
                        <td>
                            @forelse((array) $row->shippment_ids as $shipmentId)
                                <a class="badge badge-outline-primary mr-1" href="{{ route('ShippmentsController@read', ['model' => $shipmentId]) }}">#{{ $shipmentId }}</a>
                            @empty — @endforelse
                        </td>
                        <td>
                            @if($row->status === 'FAILED')
                                <ul class="shipment-import-error-list">
                                @foreach((array) $row->errors as $field => $messages)
                                    @foreach((array) $messages as $message)<li>{{ $message }}</li>@endforeach
                                @endforeach
                                </ul>
                            @else — @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-soft py-5">{{ __('contenttypes::strings.shipment_import.waiting_for_rows') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@if(in_array($import->status, ['PENDING', 'PROCESSING']))
@push('script')
<script>
    (function () {
        const monitor = document.getElementById('shipment-import-monitor');
        if (!monitor) return;

        const initialStatus = monitor.dataset.status;
        const statusUrl = monitor.dataset.statusUrl;
        let checking = false;

        const setText = function (id, value) {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        };

        const checkStatus = function () {
            if (checking || document.hidden) return;
            checking = true;

            fetch(statusUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store'
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Unable to read import status.');
                    return response.json();
                })
                .then(function (data) {
                    if (data.status !== initialStatus) {
                        window.location.reload();
                        return;
                    }

                    setText('shipment-import-created-count', data.created_count);
                    setText('shipment-import-updated-count', data.updated_count);
                    setText('shipment-import-failed-count', data.failed_count);
                    setText('shipment-import-processed-rows', data.processed_rows);
                    setText('shipment-import-total-rows', data.total_rows);

                    if (data.total_rows > 0) {
                        const progress = document.getElementById('shipment-import-progress');
                        const progressBar = document.getElementById('shipment-import-progress-bar');
                        if (progress) progress.classList.remove('d-none');
                        if (progressBar) {
                            progressBar.style.width = Math.min(100, Math.round((data.processed_rows / data.total_rows) * 100)) + '%';
                        }
                    }
                })
                .catch(function () {
                    // Keep the current page stable; the next poll will retry silently.
                })
                .finally(function () {
                    checking = false;
                });
        };

        window.setInterval(checkStatus, 5000);
        document.addEventListener('visibilitychange', checkStatus);
    })();
</script>
@endpush
@endif
@endsection
