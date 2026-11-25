@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.shipment_import.import_shipments'),
        'items' => [
            ['title' => __('contenttypes::strings.shippments.list'), 'link' => route('ShippmentsController@index')],
            ['title' => __('contenttypes::strings.shipment_import.import_history'), 'link' => route('ShippmentsController@imports')],
        ],
    ]
])
@endcomponent

@include('contenttypes::shippments.imports._styles')

@section('content')
<div class="container-fluid mt-4">
    <div class="row g-gs">
        @if(auth()->user()->type === 'DEFAULT')
        <div class="col-lg-4">
            <div class="card shipment-import-card h-100">
                <div class="card-inner">
                    <div class="shipment-import-heading">
                        <div class="shipment-import-icon"><em class="icon ni ni-upload-cloud"></em></div>
                        <div>
                            <h5 class="mb-1">{{ __('contenttypes::strings.shipment_import.upload_title') }}</h5>
                            <p class="text-soft mb-0">{{ __('contenttypes::strings.shipment_import.upload_hint') }}</p>
                        </div>
                    </div>
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('ShippmentsController@postImport') }}" enctype="multipart/form-data">
                        @csrf
                        @include('ui::components.input', [
                            'options' => [
                                'id'          => 'shipment-import-file',
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'file',
                                'label'       => __('contenttypes::strings.shipment_import.select_file'),
                                'value'       => __('contenttypes::strings.shipment_import.choose_file'),
                                'placeholder' => __('contenttypes::strings.shipment_import.choose_file'),
                                'attributes'  => ['accept' => '.xlsx', 'required' => 'required'],
                            ],
                        ])
                        <span class="form-note mt-2 mb-3">{{ __('contenttypes::strings.shipment_import.file_requirements') }}</span>
                        <div class="shipment-import-actions">
                            <button class="btn btn-primary" type="submit">
                                <em class="icon ni ni-upload"></em><span>{{ __('contenttypes::strings.shipment_import.start_import') }}</span>
                            </button>
                            <a class="btn btn-outline-light" href="{{ route('ShippmentsController@downloadImportTemplate') }}">
                                <em class="icon ni ni-download-cloud"></em><span>{{ __('contenttypes::strings.shipment_import.download_template') }}</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        <div class="{{ auth()->user()->type === 'DEFAULT' ? 'col-lg-8' : 'col-lg-12' }}">
            <div class="card shipment-import-card shipment-import-history">
                <div class="card-inner border-bottom"><h5 class="mb-0">{{ __('contenttypes::strings.shipment_import.import_history') }}</h5></div>
                <div class="table-responsive">
                    <table class="table table-middle mb-0">
                        <thead><tr>
                            <th>#</th><th>{{ __('contenttypes::strings.shipment_import.file') }}</th>
                            <th>{{ __('contenttypes::strings.shipment_import.imported_by') }}</th>
                            <th>{{ __('contenttypes::strings.shipment_import.status') }}</th>
                            <th>{{ __('contenttypes::strings.shipment_import.results') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                        @forelse($imports as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td class="shipment-import-file"><strong>{{ $item->original_filename }}</strong><div class="text-soft small">{{ $item->created_at }}</div></td>
                                <td>{{ optional($item->user)->name ?: optional($item->user)->email }}</td>
                                <td><span class="badge badge-{{ in_array($item->status, ['COMPLETED']) ? 'success' : ($item->status === 'COMPLETED_WITH_ERRORS' || $item->status === 'FAILED' ? 'danger' : 'warning') }}">{{ __('contenttypes::strings.shipment_import.statuses.' . $item->status) }}</span></td>
                                <td><div class="shipment-import-result">
                                    <span class="text-success"><em class="icon ni ni-plus-circle"></em>{{ $item->created_count }}</span>
                                    <span class="text-primary"><em class="icon ni ni-edit-alt"></em>{{ $item->updated_count }}</span>
                                    <span class="text-danger"><em class="icon ni ni-cross-circle"></em>{{ $item->failed_count }}</span>
                                </div>
                                </td>
                                <td><a class="btn btn-sm btn-outline-primary" href="{{ route('ShippmentsController@readImport', ['import' => $item->id]) }}">{{ __('contenttypes::strings.shipment_import.view_report') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-soft py-5">{{ __('contenttypes::strings.shipment_import.no_imports') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($imports->hasPages())<div class="card-inner">{{ $imports->links() }}</div>@endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).on('change', '#shipment-import-file', function () {
        const fileName = this.files && this.files.length ? this.files[0].name : @json(__('contenttypes::strings.shipment_import.choose_file'));
        $(this).siblings('.custom-file-label').text(fileName);
    });
</script>
@endpush
