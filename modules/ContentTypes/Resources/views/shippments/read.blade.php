@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'lg',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::orders.order_details'),
        'items' => [
            [
                'title' => __('contenttypes::orders.orders'),
                'link'  => route('ShippmentsController@index')
            ],
            [
                'title' => __('contenttypes::orders.order_details'),
                'link'  => route('ShippmentsController@read', ['model' => $model->id])
            ]
        ],
    ]
])
    
@endcomponent

@section('content')

<div class="order-details">
    <div class="container-fluid">
        <div class="alert order-alert">
            <div>
                ⚠️ &nbsp;  {{__('contenttypes::orders.caution')}}
            </div>
            <p>
                There is no transporter has been selected and you must either add a new transporter with a contract or send the request as an RFQ.
            </p>
        </div>
    </div>
    {{-- <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-6">
                <div class="nk-content-inner">
                    <div class="card card-bordered border-unset-shadow">
                        <div class="card-inner">
                            <div class="card-1">
                                <div class="user-card">
                                    <div class="">
                                        <img src="{{ asset('images/icons/truck.svg') }}" alt="">
                                    </div>
                                    <div class="user-name">
                                        {{__('contenttypes::orders.browse_all_transports')}}
                                    </div>
                                </div>
                                <p>
                                    {{__('contenttypes::orders.browse_all_transports_desc')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container-fluid mt-4 order-info">
        <div class="row">
            <div class="col-lg-12">
                <div class="nk-content-inner">
                    <div class="card card-bordered h-100 border-unset-shadow">
                        <div class="card-inner">
                            <div class="card-title-group align-start gx-3 mb-3 main-title">
                                <div class="card-title">
                                    <h6 class="custom-title">{{__('contenttypes::orders.order_details')}}</h6>
                                    <p>{{__('contenttypes::orders.details_of_the_order')}}</p>
                                </div>
                                <div class="card-tools">
                                    @if(auth()->check() && auth()->user()->type == 'TRANSPORTER')
                                        @if($model->sys_status == 'PENDING_TRANSPORTER_APPROVAL' && $matchedTransportar && $matchedTransportar->status == 'PENDING_RESPONSE')
                                            <div class="dropdown">
                                                <a href="{{ route('ShippmentsController@transportarApprove', ['model' => $model->id]) }}" class="btn btn-success btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-check"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.APPROVE')}}
                                                    </span>
                                                </a>
                                            </div>
                                            {{-- <div class="dropdown">
                                                <a href="javascript:;" class="btn btn-warning btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-info"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.APPROVE_WITH_PRICE_CHANGE')}}
                                                    </span>
                                                </a>
                                            </div> --}}
                                            <div class="dropdown">
                                                <a href="{{ route('ShippmentsController@transportarReject', ['model' => $model->id]) }}" class="btn btn-danger btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-cross"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.DECLINE')}}
                                                    </span>
                                                </a>
                                            </div>
                                        @elseif($matchedTransportar && $matchedTransportar->status == 'APPROVED')
                                            <div class="dropdown">
                                                <a href="javascript:;" class="btn btn-warning btn-lg d-sm-inline-flex bg-orange" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-download-cloud"></em><span>Waybill</span>
                                                </a>
                                            </div>
                                        @else
                                            
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contenttypes::orders.general_information')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/weight.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.weight')}}</span>
                                                <p class="value">
                                                    {{ $model->total_weight }}
                                                    {{__('contenttypes::orders.ton')}}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.DeclaredLoadValue')}}</span>
                                                <p class="value">
                                                    {{ $helper->moneyFormat($model->decalred_value, 2)->abs }} SAR
                                                </p>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/Commodity.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.Commodity')}}</span>
                                                <p class="btns">
                                                    @foreach ($model->commodities as $item)
                                                        <a href="javascript:;" class="btn btn-custom-1">{{ $item->commodity->__('title') }}</a>
                                                    @endforeach
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contenttypes::orders.order_specifications')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/load_size.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.load_size')}}</span>
                                                <p class="value">
                                                    {{ $model->loadSize->__('title') }}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/wehicle_condition.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.wehicle_condition')}}</span>
                                                <p class="value">
                                                    {{ $model->loadType->__('title') }}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/number_of_trucks.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.number_of_trucks')}}</span>
                                                <p class="value">
                                                    {{ $helper->moneyFormat($model->number_of_trucks, 0)->abs }} 
                                                    {{__('contenttypes::orders.trucks')}}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" class="modify-icon-1">
                                                <span class="name">{{__('contenttypes::orders.number_of_pallets')}}</span>
                                                <p class="value">
                                                    {{  $helper->moneyFormat($model->number_of_pallets, 0)->abs }} 
                                                    {{__('contenttypes::orders.pallets')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-12">
                                    <h4>
                                        {{__('contenttypes::orders.equipment_details')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-6 mb-4">
                                                <img src="{{ asset('images/icons/truck_type.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.truck_type')}}</span>
                                                <p class="value">
                                                    {{ $model->truckType->__('title') }}
                                                </p>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-4">
                                                <img src="{{ asset('images/icons/temperature.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.temperature')}}</span>
                                                <p class="value">
                                                    {{  $helper->moneyFormat($model->temperature, 2)->real }}°C
                                                </p>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-4">
                                                <img src="{{ asset('images/icons/equipment_length.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.equipment_length')}}</span>
                                                <p class="value">
                                                    {{  $helper->moneyFormat($model->equipment_length, 2)->abs }} 
                                                    {{__('contenttypes::orders.meters')}}
                                                </p>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-4">
                                                <img src="{{ asset('images/icons/equipment_length.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.equipment_width')}}</span>
                                                <p class="value">
                                                    {{  $helper->moneyFormat($model->equipment_width, 2)->abs }} 
                                                    {{__('contenttypes::orders.meters')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contenttypes::orders.public_notes')}}
                                    </h4>
                                    <div class="textarea">
                                        <textarea class="form-control form-control-simple" placeholder="{{__('contenttypes::orders.public_notes')}}" readonly>{{ $model->public_notes ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contenttypes::orders.private_notes')}}
                                    </h4>
                                    <div class="textarea">
                                        <textarea class="form-control form-control-simple" placeholder="{{__('contenttypes::orders.private_notes')}}" readonly>{{ $model->private_notes ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @if($model->loadingLocation)
                                <div class="row general-info">
                                    <div class="col-lg-6">
                                        <h4>
                                            {{__('contenttypes::orders.loading_points_section')}}
                                        </h4>
                                        <div class="box">
                                            <div class="row">
                                                <div class="col-lg-12 mb-4">
                                                    <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.customer_name')}}</span>
                                                    <p class="value">
                                                        {{ $model->loadingLocation->primary_contact_company_name ?? '' }}
                                                    </p>
                                                </div>
                                                <div class="col-lg-6 mb-4">
                                                    <img src="{{ asset('images/icons/source_name.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.source_name')}}</span>
                                                    <p class="value">
                                                        {{ $model->loadingLocation->primary_contact_company_name ?? '' }}
                                                    </p>
                                                </div>
                                                <div class="col-lg-6 mb-4">
                                                    <img src="{{ asset('images/icons/city.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.city')}}</span>
                                                    <p class="value">
                                                        {{ $model->loadingLocation->city->{'name_' . app()->getLocale()} ?? '' }}
                                                    </p>
                                                </div>
                                                @if($model->loadingLocation->public_notes)
                                                    <div class="col-lg-6 mb-4 map">
                                                        <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                            <div class="card-title">
                                                                <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                                                                <span class="name">{{__('contenttypes::orders.public_notes')}}</span>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            {{ $model->loadingLocation->public_notes ?? '' }}
                                                        </p>
                                                    </div>
                                                @endif
                                                @if($model->loadingLocation->private_notes)
                                                    <div class="col-lg-6 mb-4 map">
                                                        <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                            <div class="card-title">
                                                                <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                                                                <span class="name">{{__('contenttypes::orders.private_notes')}}</span>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            {{ $model->loadingLocation->private_notes ?? '' }}
                                                        </p>
                                                    </div>
                                                @endif                                                
                                                <div class="col-lg-12 mb-4 map">
                                                    <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                        <div class="card-title">
                                                            <img src="{{ asset('images/icons/address.svg') }}" alt="">
                                                            <span class="name">{{__('contenttypes::orders.address')}}</span>
                                                        </div>
                                                        <div class="card-tools view-on-map">
                                                            @if($model->loadingLocation->lat && $model->loadingLocation->lng)
                                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $model->loadingLocation->lat }},{{ $model->loadingLocation->lng }}" target="_blank">{{__('contenttypes::orders.view_on_map')}}</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p>
                                                        {{ $model->loadingLocation->full_address ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h4>
                                            {{__('contenttypes::orders.loading_contact_details')}}
                                        </h4>
                                        <div class="box">
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.primary_contact_person')}}</span>
                                                <p class="value">
                                                    {{ $model->loadingLocation->primary_contact_name ?? '' }}
                                                </p>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.phone')}}</span>
                                                <p class="value" style="direction: ltr">
                                                    {{ $model->loadingLocation->primary_contact_phone_number_ext ?? '' }}
                                                    {{ $model->loadingLocation->primary_contact_phone_number ?? '' }}
                                                </p>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::orders.email_address')}}</span>
                                                <p class="value">
                                                    {{ $model->loadingLocation->primary_contact_email ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="row general-info">
                                <div class="col-lg-12">
                                    <h4>
                                        {{__('contenttypes::orders.offloading_points_section')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 mb-4">
                                                <div class="card-title-group align-start gx-3 mb-3 main-title">
                                                    <div class="card-title">
                                                        <img src="{{ asset('images/icons/loading_type.svg') }}" alt="">
                                                        <span class="name">{{__('contenttypes::orders.loading_type')}}</span>
                                                        <p class="value">
                                                            @php
                                                                $type = collect(config('contenttypes.loadingTypes'))->where('id', $model->type)->first();
                                                            @endphp
                                                            @if($type)
                                                                {{ __($type['label']) }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="card-tools">
                                                        @if($model->round_trip == 'YES')
                                                            <span class="badge badge-lg badge-primary">{{__('contenttypes::orders.round_trip')}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach ($model->offloadingLocations as $key => $item)
                                                @include('contenttypes::shippments.location', [
                                                    'shippment' => $model,
                                                    'from'      => $model->loadingLocation,
                                                    'to'        => $item,
                                                    'index'     => $key,
                                                    'type'      => 'offloading',
                                                ])
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        
    </script>
@endpush