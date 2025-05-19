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
        {{-- <div class="container-fluid">
            <div class="alert order-alert">
                <div>
                    ⚠️ &nbsp;  {{__('contenttypes::orders.caution')}}
                </div>
                <p>
                    There is no transporter has been selected and you must either add a new transporter with a contract or send the request as an RFQ.
                </p>
            </div>
        </div> --}}
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

                                        @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $matchedTransportar)

                                            @if($model->sys_status == 'PENDING_TRANSPORTER_APPROVAL' && $matchedTransportar->status == 'PENDING_RESPONSE')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-success btn-md d-sm-inline-flex supplier-load-pricing" data-toggle="modal" data-target="#supplier-approve-modal">
                                                        <em class="icon ni ni-check"></em>
                                                        <span>
                                                            {{__('contenttypes::inputs.actions.APPROVE')}}
                                                        </span>
                                                    </button>
                                                </div>
                                                <div class="dropdown">
                                                    <a href="{{ route('ShippmentsController@transportarReject', ['model' => $model->id]) }}" class="btn btn-danger btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-cross"></em>
                                                        <span>
                                                            {{__('contenttypes::inputs.actions.DECLINE')}}
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                                                                            
                                            @if($matchedTransportar->status == 'ACCEPTED' && $model->sys_status == 'PENDING_PICKUP')
                                                @if (! $model->WAYBILL || ! $model->driver)
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="btn btn-warning btn-lg bg-orange" data-toggle="modal" data-target="#supplier-waybill-and-driver-modal" onclick="fetchWaybillAndDriver({{ $model->id }})">
                                                            <i class="icon fas fa-upload" style="margin-left: 5px; margin-right: 5px;"></i>
                                                            <span>{{ __('contenttypes::inputs.waybill.upload') }}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                                @if ($model->WAYBILL && $model->driver)
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="btn btn-warning btn-lg bg-orange" data-toggle="modal" data-target="#supplier-waybill-and-driver-view-modal" onclick="fetchWaybillAndDriverView({{ $model->id }})">
                                                            <i class="icon fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                                            <span>{{ __('contenttypes::inputs.waybill.view_file') }}</span>
                                                        </a>
                                                    </div>   
                                                @endif
                                            @endif
                                        @endif
                                        @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $model->is_rfq == 'YES' && $matchedRfqTransportar)
                                            @if($model->sys_status == 'RFQ_PENDING_TRANSPORTER_OFFER' && $matchedRfqTransportar->status == 'PENDING_RESPONSE')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-success btn-md d-sm-inline-flex supplier-load-pricing" data-toggle="modal" data-target="#supplier-rfq-approve-modal">
                                                        <em class="icon ni ni-check"></em>
                                                        <span>
                                                            {{__('contenttypes::inputs.actions.APPROVE')}}
                                                        </span>
                                                    </button>
                                                </div>
                                                <div class="dropdown">
                                                    <a href="{{ route('ShippmentsController@transportarRfqReject', ['model' => $model->id]) }}" class="btn btn-danger btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-cross"></em>
                                                        <span>
                                                            {{__('contenttypes::inputs.actions.DECLINE')}}
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                        @endif

                                        @if(auth()->check() && auth()->user()->type == 'DEFAULT')
                                            @if ($model->sys_status == 'TRANSPORTER_APPROVED_WITH_PRICE_CHANGE')
                                                <a href="{{ route('ShippmentsController@clientApprovePriceChange', ['model' => $model->id]) }}" class="btn btn-success btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-check"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.APPROVE_PRICE_CHANGE')}}
                                                    </span>
                                                </a>
                                                <a href="{{ route('ShippmentsController@clientRejectPriceChange', ['model' => $model->id]) }}" class="btn btn-danger btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-cross"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.DECLINE_PRICE_CHANGE')}}
                                                    </span>
                                                </a>
                                            @endif
                                        @endif

                                        @if(auth()->check() && auth()->user()->type == 'DEFAULT' && $model->is_rfq == 'YES' && $matchedRfqTransportar)
                                            @if ($model->sys_status == 'RFQ_PENDING_TRANSPORTER_OFFER' && $matchedRfqTransportar->status == 'PENDING_ACCEPTANCE')
                                                <a href="{{ route('ShippmentsController@clientApproveRfq', ['model' => $model->id, 'rfq_id' => $matchedRfqTransportar->id]) }}" class="btn btn-success btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-check"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.APPROVE_RFQ')}}
                                                    </span>
                                                </a>
                                                <a href="{{ route('ShippmentsController@clientRejectRfq', ['model' => $model->id, 'rfq_id' => $matchedRfqTransportar->id]) }}" class="btn btn-danger btn-md d-sm-inline-flex" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-cross"></em>
                                                    <span>
                                                        {{__('contenttypes::inputs.actions.DECLINE_RFQ')}}
                                                    </span>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                @if ($model->is_rfq == 'NO')
                                    <div class="row general-info">
                                        @if(auth()->check() && auth()->user()->type == 'DEFAULT')
                                            <div class="col-lg-6">
                                                <h4>
                                                    {{__('contenttypes::inputs.transportar_details.label')}}
                                                </h4>
                                                @include('contenttypes::shippments.company_user', [
                                                    'model'       => $model,
                                                    'company'     => $model->transporterUser ? $model->transporterUser->company : null,
                                                ])
                                            </div>
                                        @endif
                                        @if(auth()->check() && auth()->user()->type == 'TRANSPORTER')
                                            <div class="col-lg-6">
                                                <h4>
                                                    {{__('contenttypes::inputs.client_details.label')}}
                                                </h4>
                                                @include('contenttypes::shippments.company_user', [
                                                    'model'       => $model,
                                                    'company'     => $model->companyUser ? $model->companyUser->company : null,
                                                ])
                                            </div>
                                        @endif
                                        <div class="col-lg-6">
                                            <h4>
                                                {{__('contenttypes::inputs.pricing_details.label')}}
                                            </h4>
                                            @include('contenttypes::shippments.pricing_details', [
                                                'model'                 => $model,
                                                'matchedRfqTransportar' => $matchedRfqTransportar ?? null,
                                            ])
                                        </div>
                                    </div>
                                @elseif($matchedRfqTransportar)
                                    <div class="row general-info">
                                        @if(auth()->check() && auth()->user()->type == 'DEFAULT')
                                            <div class="col-lg-6">
                                                <h4>
                                                    {{__('contenttypes::inputs.transportar_details.label')}}
                                                </h4>
                                                @include('contenttypes::shippments.company_user', [
                                                    'model'       => $model,
                                                    'company'     => $matchedRfqTransportar && $matchedRfqTransportar->transporterUser 
                                                        ? ($matchedRfqTransportar->transporterUser->company) : ($model->transporterUser ? $model->transporterUser->company : null),
                                                ])
                                            </div>
                                        @endif
                                        @if(auth()->check() && auth()->user()->type == 'TRANSPORTER')
                                            <div class="col-lg-6">
                                                <h4>
                                                    {{__('contenttypes::inputs.client_details.label')}}
                                                </h4>
                                                @include('contenttypes::shippments.company_user', [
                                                    'model'       => $model,
                                                    'company'     => $model->companyUser ? $model->companyUser->company : null,
                                                ])
                                            </div>
                                        @endif
                                        <div class="col-lg-6">
                                            <h4>
                                                {{__('contenttypes::inputs.pricing_details.label')}}
                                            </h4>
                                            @include('contenttypes::shippments.pricing_details', [
                                                'model'                 => $model,
                                                'matchedRfqTransportar' => $matchedRfqTransportar ?? null,
                                            ])
                                        </div>
                                    </div>
                                @endif
                                

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
                                                <div class="col-lg-3 col-md-6 mt-4">
                                                    <img src="{{ asset('images/icons/truck_type.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.truck_type')}}</span>
                                                    <p class="value">
                                                        {{ $model->truckType->__('title') }}
                                                    </p>
                                                </div>
                                                <div class="col-lg-3 col-md-6 mt-4">
                                                    <img src="{{ asset('images/icons/temperature.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.temperature')}}</span>
                                                    <p class="value">
                                                        {{  $helper->moneyFormat($model->temperature, 2)->real }}°C
                                                    </p>
                                                </div>
                                                <div class="col-lg-3 col-md-6 mt-4">
                                                    <img src="{{ asset('images/icons/equipment_length.svg') }}" alt="">
                                                    <span class="name">{{__('contenttypes::orders.equipment_length')}}</span>
                                                    <p class="value">
                                                        {{  $helper->moneyFormat($model->equipment_length, 2)->abs }} 
                                                        {{__('contenttypes::orders.meters')}}
                                                    </p>
                                                </div>
                                                <div class="col-lg-3 col-md-6 mt-4">
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
                                                <div class="col-lg-12 mb-4">
                                                    @include('contenttypes::shippments.progress', [
                                                        'shippment' => $model,
                                                        'from'      => $model->loadingLocation,
                                                        'locations' => $model->offloadingLocations,
                                                    ])
                                                </div>
                                                @foreach ($model->offloadingLocations as $key => $offloadingLocation)
                                                    @include('contenttypes::shippments.location', [
                                                        'shippment' => $model,
                                                        'from'      => $model->loadingLocation,
                                                        'to'        => $offloadingLocation,
                                                        'index'     => $key,
                                                        'type'      => 'offloading',
                                                    ])
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <h4>
                                            {{__('contenttypes::inputs.driver_details.label')}}
                                        </h4>
                                        @include('contenttypes::shippments.driver_details', [
                                            'model'      => $model,
                                            'driver'     => $model->driver,
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        <h4>
                                            {{__('contenttypes::inputs.attachments.label')}}
                                        </h4>
                                        @include('contenttypes::shippments.files', [
                                            'model'       => $model,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $matchedTransportar)
        @component('ui::components.form', [
            'options' => [
                'id'                => 'supplier-approve-app',
                'action'            => route('ShippmentsController@transportarApproveSwitch'),
                'method'            => 'POST',
                'class'             => '',
                'attributes'        => [
                    'onsubmit'                     => 'BS.AJAX_FORM(event)',
                    'data-keep-loading-on-success' => 'false',
                ],
            ],
        ])

            @component('ui::components.modal', [
                'options' => [
                    'id'        => 'supplier-approve-modal',
                    'title'     => __('contenttypes::inputs.actions.APPROVE'),
                    'size'      => '',
                    'fit'       => false,
                ]
            ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'mode',
                        'attributes'  => [
                            'v-model' => 'mode',
                        ]
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'model',
                        'value'       => $model->id,
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'type',
                        'attributes'  => [
                            'v-model' => 'original_pricing_item.type',
                        ]
                    ],
                ])
                
                <div class="text-center m-5" v-if="is_loading">
                    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
                </div>

                <div v-if="!is_loading && mode == 'original'">
                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'price',
                            'label'       => __('contenttypes::inputs.price.label'),
                            'placeholder' => __('contenttypes::inputs.price.placeholder'),
                            'attributes'  => [
                                'v-model'  => 'original_pricing_item.price',
                                'disabled' => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'pre_additional_location_price',
                            'label'       => __('contenttypes::inputs.pre_additional_location_price.label'),
                            'placeholder' => __('contenttypes::inputs.pre_additional_location_price.placeholder'),
                            'attributes'  => [
                                'v-model'  => 'original_pricing_item.pre_additional_location_price',
                                'disabled' => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'additional_locations_count',
                            'label'       => __('contenttypes::inputs.additional_locations_count.label'),
                            'placeholder' => __('contenttypes::inputs.additional_locations_count.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'original_shippment_pricing.locations_count',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">{{ __('contenttypes::inputs.additional_locations_count.addon') }}</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    <hr class="preview-hr">

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'shippment_total_price',
                            'label'       => __('contenttypes::inputs.shippment_total_price.label'),
                            'placeholder' => __('contenttypes::inputs.shippment_total_price.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'original_shippment_pricing.total_price',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    <div class="d-flex justify-end mt-2">
                        <a class="link link-danger link-sm" tabindex="-1" href="javascript:;" v-on:click="changeMode('new')">
                            <i class="fas fa-edit" style="margin-left: 5px; margin-right: 5px;"></i>
                            {{ __('contenttypes::inputs.change_price.label') }}
                        </a>
                    </div>
                </div>

                <div v-if="!is_loading && mode != 'original'">
                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'price',
                            'label'       => __('contenttypes::inputs.price.label'),
                            'placeholder' => __('contenttypes::inputs.price.placeholder'),
                            'attributes'  => [
                                'v-model'  => 'new_pricing_item.price',
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'pre_additional_location_price',
                            'label'       => __('contenttypes::inputs.pre_additional_location_price.label'),
                            'placeholder' => __('contenttypes::inputs.pre_additional_location_price.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'new_pricing_item.pre_additional_location_price',
                                ':disabled' => "original_pricing_item.type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS'",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'additional_locations_count',
                            'label'       => __('contenttypes::inputs.additional_locations_count.label'),
                            'placeholder' => __('contenttypes::inputs.additional_locations_count.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'original_shippment_pricing.locations_count',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">{{ __('contenttypes::inputs.additional_locations_count.addon') }}</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    <hr class="preview-hr">

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'shippment_total_price',
                            'label'       => __('contenttypes::inputs.shippment_total_price.label'),
                            'placeholder' => __('contenttypes::inputs.shippment_total_price.placeholder'),
                            'attributes'  => [
                                ':value'    => 'Number(Number(new_pricing_item.price) + (Number((Number(original_shippment_pricing.locations_count))) * Number(new_pricing_item.pre_additional_location_price))).toFixed(2)',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    <div class="d-flex justify-end mt-2">
                        <a class="link link-success link-sm" tabindex="-1" href="javascript:;" v-on:click="changeMode('original')">
                            <i class="fas fa-edit" style="margin-left: 5px; margin-right: 5px;"></i>
                            {{ __('contenttypes::inputs.change_price.back') }}
                        </a>
                    </div>
                </div>

                <x-slot name="footer">
                    <div v-if="!is_loading && mode != 'original'">
                        <button type="submit" class="btn btn-sm btn-success">
                            <span>{{ __('contenttypes::inputs.change_price.save_new_price') }}</span>
                        </button>
                    </div>
                    <div v-if="!is_loading && mode == 'original'">
                        <button type="submit" class="btn btn-sm btn-success">
                            <span>{{ __('contenttypes::inputs.change_price.save_original_price') }}</span>
                        </button>
                    </div>

                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                        <span>{{ __('ui::strings.cancel') }}</span>
                    </button>
                </x-slot>
            @endcomponent
        @endcomponent
    @endif

    @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $model->is_rfq == 'YES' && $matchedRfqTransportar)
        @component('ui::components.form', [
            'options' => [
                'id'                => 'supplier-rfq-approve-app',
                'action'            => route('ShippmentsController@transportarRfqApprove'),
                'method'            => 'POST',
                'class'             => '',
                'attributes'        => [
                    'onsubmit'                     => 'BS.AJAX_FORM(event)',
                    'data-keep-loading-on-success' => 'false',
                ],
            ],
        ])

            @component('ui::components.modal', [
                'options' => [
                    'id'        => 'supplier-rfq-approve-modal',
                    'title'     => __('contenttypes::inputs.actions.APPROVE'),
                    'size'      => '',
                    'fit'       => false,
                ]
            ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'mode',
                        'attributes'  => [
                            'v-model' => 'mode',
                        ]
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'model',
                        'value'       => $model->id,
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'type',
                        'attributes'  => [
                            'v-model' => 'original_pricing_item.type',
                        ]
                    ],
                ])
                
                <div v-if="!is_loading && mode != 'original'">
                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'price',
                            'label'       => __('contenttypes::inputs.price.label'),
                            'placeholder' => __('contenttypes::inputs.price.placeholder'),
                            'attributes'  => [
                                'v-model'  => 'new_pricing_item.price',
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'pre_additional_location_price',
                            'label'       => __('contenttypes::inputs.pre_additional_location_price.label'),
                            'placeholder' => __('contenttypes::inputs.pre_additional_location_price.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'new_pricing_item.pre_additional_location_price',
                                ':disabled' => "original_pricing_item.type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS'",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'additional_locations_count',
                            'label'       => __('contenttypes::inputs.additional_locations_count.label'),
                            'placeholder' => __('contenttypes::inputs.additional_locations_count.placeholder'),
                            'attributes'  => [
                                'v-model'   => 'original_shippment_pricing.locations_count',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">{{ __('contenttypes::inputs.additional_locations_count.addon') }}</small>
                            </div>
                        </x-slot>
                    @endcomponent

                    <hr class="preview-hr">

                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'name'        => 'shippment_total_price',
                            'label'       => __('contenttypes::inputs.shippment_total_price.label'),
                            'placeholder' => __('contenttypes::inputs.shippment_total_price.placeholder'),
                            'attributes'  => [
                                ':value'    => 'Number(Number(new_pricing_item.price) + (Number((Number(original_shippment_pricing.locations_count))) * Number(new_pricing_item.pre_additional_location_price))).toFixed(2)',
                                // ':value'    => 'Number((Number(original_shippment_pricing.locations_count) - 1))',
                                'disabled'  => "true",
                            ]
                        ],
                    ])
                        <x-slot name="after">
                            <div class="form-text-hint">
                                <small class="overline-title">SAR</small>
                            </div>
                        </x-slot>
                    @endcomponent
                </div>

                <x-slot name="footer">
                    <div v-if="!is_loading && mode != 'original'">
                        <button type="submit" class="btn btn-sm btn-success">
                            <span>{{ __('contenttypes::inputs.change_price.save_new_price') }}</span>
                        </button>
                    </div>
                    <div v-if="!is_loading && mode == 'original'">
                        <button type="submit" class="btn btn-sm btn-success">
                            <span>{{ __('contenttypes::inputs.change_price.save_original_price') }}</span>
                        </button>
                    </div>

                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                        <span>{{ __('ui::strings.cancel') }}</span>
                    </button>
                </x-slot>
            @endcomponent
        @endcomponent
    @endif

    @component('ui::components.form', [
        'options' => [
            'id'                => 'supplier-waybill-and-driver-app',
            'action'            => route('ShippmentsController@postDriverAndWaybill'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'postDriverAndWaybill(event)',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'supplier-waybill-and-driver-modal',
                'title'     => __('contenttypes::inputs.waybill.label'),
                'size'      => 'lg',
                'fit'       => false,
            ]
        ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'mode',
                    'attributes'  => [
                        'v-model' => 'mode',
                    ]
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'model',
                    'value'       => null,
                    'attributes'  => [
                        'v-model' => 'model',
                    ]
                ],
            ])
            
            <div class="text-center m-5" v-if="is_loading">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
            </div>

            <div v-show="!is_loading">
                <div class="row gy-3">
                    <div class="col-lg-12">
                        @include('ui::components.radios', [
                            'options' => [
                                'view'              => 'DEFAULT',
                                'type'              => 'radio',
                                'name'              => 'driver_mode',
                                'label'             => __('contenttypes::inputs.driver_mode.description'),
                                'placeholder'       => __('contenttypes::inputs.driver_mode.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [
                                    [
                                        'id'       => 'driver_mode_cs_1',
                                        'value'    => 'existing',
                                        'label'    => __('contenttypes::inputs.driver_mode.options.existing'),
                                        'selected' => true,
                                    ],
                                    [
                                        'id'       => 'driver_mode_cs_2',
                                        'value'    => 'new',
                                        'label'    => __('contenttypes::inputs.driver_mode.options.new'),
                                        'selected' => false,
                                    ],
                                ],
                                'option_id'         => function($key, $option)                   { return $option['id']; },
                                'option_text'       => function($key, $option)                   { return $option['label']; },
                                'option_sub_text'   => function($key, $option)                   { return null; },
                                'option_value'      => function($key, $option)                   { return $option['value']; },
                                'option_attributes' => function($key, $option)                   { 
                                    return [
                                        'v-model' => 'mode',
                                    ]; 
                                },
                                'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>

                    <div class="col-lg-6" v-if="mode == 'new'">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_name',
                                'label'       => __('contenttypes::inputs.driver_name.label'),
                                'placeholder' => __('contenttypes::inputs.driver_name.placeholder'),
                                'attributes'  => []
                            ],
                        ])
                    </div>
                    <div class="col-lg-6" v-if="mode == 'new'">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_id_number',
                                'label'       => __('contenttypes::inputs.driver_id_number.label'),
                                'placeholder' => __('contenttypes::inputs.driver_id_number.placeholder'),
                                'attributes'  => []
                            ],
                        ])
                    </div>
                    <div class="col-md-6" v-show="mode == 'new'">
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'country_select',
                                'jsSelect2'         => 'countryJsSelect2',
                                'view'              => 'DEFAULT',
                                'name'              => 'driver_nationality_country_id',
                                'label'             => __('contenttypes::inputs.driver_nationality_country_id.label'),
                                'placeholder'       => __('contenttypes::inputs.driver_nationality_country_id.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>

                    <div class="col-md-6" v-show="mode == 'existing'">
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'driver_id_select',
                                'jsSelect2'         => 'driver_id_select2',
                                'view'              => 'DEFAULT',
                                'name'              => 'driver_id',
                                'label'             => __('contenttypes::inputs.driver_id.label'),
                                'placeholder'       => __('contenttypes::inputs.driver_id.placeholder'),
                                'attributes'        => [],
                                'value'             => null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
                    </div>

                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'car_plate',
                                'label'       => __('contenttypes::inputs.car_plate.label'),
                                'placeholder' => __('contenttypes::inputs.car_plate.placeholder'),
                                'attributes'  => [
                                    'v-model' => 'car_plate',
                                ]
                            ],
                        ])
                    </div>

                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'WAYBILL',
                                'label'       => __('contenttypes::inputs.waybill.label'),
                                'description' => __('contenttypes::inputs.attachment_1.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])
                    </div>
                    <div class="col-lg-6" v-if="waybill">
                        <div class="form-group">
                            <label>{{ __('contenttypes::inputs.waybill.label') }}</label>
                            <div class="form-control-wrap">
                                <a :href="waybill.path" class="btn btn-sm btn-success btn-block" target="_blank">
                                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                    <span>{{ __('contenttypes::inputs.waybill.view_file') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div v-if="!is_loading && mode != 'existing'">
                    <button type="submit" class="btn btn-sm btn-success">
                        <span>{{ __('contenttypes::inputs.waybill.upload') }}</span>
                    </button>
                </div>
                <div v-if="!is_loading && mode == 'existing'">
                    <button type="submit" class="btn btn-sm btn-success">
                        <span>{{ __('contenttypes::inputs.waybill.upload') }}</span>
                    </button>
                </div>

                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

    @component('ui::components.form', [
        'options' => [
            'id'                => 'supplier-waybill-and-driver-view-app',
            'action'            => route('ShippmentsController@postDriverAndWaybill'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'BS.AJAX_FORM(event)',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'supplier-waybill-and-driver-view-modal',
                'title'     => __('contenttypes::inputs.waybill.label'),
                'size'      => 'lg',
                'fit'       => false,
            ]
        ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'mode',
                    'attributes'  => [
                        'v-model' => 'mode',
                    ]
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'model',
                    'value'       => null,
                    'attributes'  => [
                        'v-model' => 'model',
                    ]
                ],
            ])
            
            <div class="text-center m-5" v-if="is_loading">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
            </div>

            <div v-show="!is_loading">
                <div class="row gy-3">
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_name',
                                'label'       => __('contenttypes::inputs.driver_name.label'),
                                'placeholder' => __('contenttypes::inputs.driver_name.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver ? driver.name : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_id_number',
                                'label'       => __('contenttypes::inputs.driver_id_number.label'),
                                'placeholder' => __('contenttypes::inputs.driver_id_number.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver ? driver.id_number : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'driver_nationality_country_id',
                                'label'       => __('contenttypes::inputs.driver_nationality_country_id.label'),
                                'placeholder' => __('contenttypes::inputs.driver_nationality_country_id.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'driver && driver.nationality ? (driver.nationality.name_en + \' / \' + driver.nationality.name_ar) : null',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'car_plate',
                                'label'       => __('contenttypes::inputs.car_plate.label'),
                                'placeholder' => __('contenttypes::inputs.car_plate.placeholder'),
                                'attributes'  => [
                                    ':value'   => 'car_plate',
                                    'disabled' => 'true',
                                ]
                            ],
                        ])
                    </div>

                    <div class="col-lg-6" v-if="waybill">
                        <div class="form-group">
                            <label>{{ __('contenttypes::inputs.waybill.label') }}</label>
                            <div class="form-control-wrap">
                                <a :href="waybill.path" class="btn btn-sm btn-success btn-block" target="_blank">
                                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                    <span>{{ __('contenttypes::inputs.waybill.view_file') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                @if (auth()->check() && auth()->user()->type == 'TRANSPORTER')
                    <div v-if="!is_loading">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#supplier-waybill-and-driver-modal" onclick="supplierWaybillAndDriverViewApp.openEditModal()" data-dismiss="modal">
                            <span>{{ __('contenttypes::inputs.waybill.edit') }}</span>
                        </button>
                    </div>
                @endif
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

@endsection

@push('script')
    @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $matchedTransportar)
        <script>
            const supplierApproveApp = new Vue({
                el: '#supplier-approve-app',
                data: {
                    mode                : 'original',
                    model               : '{{ $model->id }}',
                    matched_pricing_id  : '{{ $matchedTransportar->id }}',
                    is_loading          : false,
                    original_pricing_item : {
                        id                            : null,
                        price                         : 0,
                        pre_additional_location_price : 0,
                        type                          : null,
                    },
                    original_shippment_pricing : {
                        price                         : {{ $model->price }},
                        pre_additional_location_price : {{ $model->pre_additional_location_price }},
                        total_price                   : {{ $model->total_price }},
                        locations_count               : {{ $model->offloadingLocations->count() > 0 ? $model->offloadingLocations->count() - 1 : 0 }},
                    },
                    new_pricing_item : {
                        price                         : 0,
                        pre_additional_location_price : 0,
                    },
                    new_shippment_pricing : {
                        price                         : {{ $model->price }},
                        pre_additional_location_price : {{ $model->pre_additional_location_price }},
                        total_price                   : {{ $model->total_price }},
                        locations_count               : {{ $model->offloadingLocations->count() > 0 ? $model->offloadingLocations->count() - 1 : 0 }},
                    },
                },
                methods: {
                    changeMode: function(mode) {
                        this.mode = mode;
                    },
                    fetchContractPrice: function() {
                        const vm = this;
                        vm.is_loading = true;
                        $.get('{{ route('ShippmentsController@pricingItem') }}', {
                            model : vm.model,
                        }, function(data) {
                            if (data['redirect_url'] || data['redirect']) {
                                window.location = data['redirect_url'] || data['redirect'];
                                return;
                            }
                            if (data['toastr']) {
                                NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                            }

                            if (data['pricing']) {
                                vm.original_pricing_item = {
                                    id                            : data['pricing'].id,
                                    price                         : data['pricing'].contract_instance_pricing.price,
                                    pre_additional_location_price : data['pricing'].contract_instance_pricing.pre_additional_location_price,
                                    type                          : data['pricing'].contract_instance_pricing.type,
                                };
                                vm.new_pricing_item = {
                                    price                         : data['pricing'].contract_instance_pricing.price,
                                    pre_additional_location_price : data['pricing'].contract_instance_pricing.pre_additional_location_price,
                                };
                            }

                            vm.is_loading = false;
                        })
                    },
                }
            });

            $(document).on('click', '.supplier-load-pricing', function(e) {
                supplierApproveApp.fetchContractPrice();
            });

            const supplierWaybillAndDriverApp = new Vue({
                el: '#supplier-waybill-and-driver-app',
                data: {
                    form       : 'edit',
                    mode       : 'existing',
                    model      : null,
                    is_loading : false,
                    driver     : null,
                    waybill    : null,
                    car_plate  : null,
                },
                methods: {
                    changeMode: function(mode) {
                        this.mode = mode;
                    },
                    resetForm: function() {
                        this.mode = 'existing';
                        this.model = null;
                    },
                    fetchWaybillAndDriver: function(model) {
                        const vm = this;
                        vm.is_loading = true;
                        vm.model      = model;
                        $.get('{{ route('ShippmentsController@driverAndWaybill') }}', {
                            model : vm.model,
                        }, function(data) {
                            if (data['redirect_url'] || data['redirect']) {
                                window.location = data['redirect_url'] || data['redirect'];
                                return;
                            }
                            if (data['toastr']) {
                                NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                            }
                            if(data['driver']) {
                                vm.driver  = data['driver'];
                            }
                            if(data['waybill']) {
                                vm.waybill = data['waybill'];
                            }
                            if(data['car_plate']) {
                                vm.car_plate = data['car_plate'];
                            }
                            vm.is_loading = false;
                        })
                    },
                    reload: function() {
                        if (this.model) {
                            this.fetchWaybillAndDriver(this.model);
                        }
                    },
                }
            });

            const fetchWaybillAndDriver = function(model) {
                supplierWaybillAndDriverApp.fetchWaybillAndDriver(model);
            }

            const supplierWaybillAndDriverViewApp = new Vue({
                el: '#supplier-waybill-and-driver-view-app',
                data: {
                    form       : 'edit',
                    mode       : 'existing',
                    model      : null,
                    is_loading : false,
                    driver     : null,
                    waybill    : null,
                    car_plate  : null,
                },
                methods: {
                    openEditModal: function() {
                        fetchWaybillAndDriver(this.model);
                    },
                    changeMode: function(mode) {
                        this.mode = mode;
                    },
                    resetForm: function() {
                        this.mode = 'existing';
                        this.model = null;
                    },
                    fetchWaybillAndDriver: function(model) {
                        const vm = this;
                        vm.is_loading = true;
                        vm.model      = model;
                        $.get('{{ route('ShippmentsController@driverAndWaybill') }}', {
                            model : vm.model,
                        }, function(data) {
                            if (data['redirect_url'] || data['redirect']) {
                                window.location = data['redirect_url'] || data['redirect'];
                                return;
                            }
                            if (data['toastr']) {
                                NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                            }
                            if(data['driver']) {
                                vm.driver  = data['driver'];
                            }
                            if(data['waybill']) {
                                vm.waybill = data['waybill'];
                            }
                            if(data['car_plate']) {
                                vm.car_plate = data['car_plate'];
                            }
                            vm.is_loading = false;
                        })
                    },
                    reload: function() {
                        if (this.model) {
                            this.fetchWaybillAndDriver(this.model);
                        }
                    },
                }
            });

            const fetchWaybillAndDriverView = function(model) {
                supplierWaybillAndDriverViewApp.fetchWaybillAndDriver(model);
            }

            var countryJsSelect2 = $('#country_select').select2({
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('CountriesController@select') }}';
                    },
                    data: function (params) {
                        var query = {
                            nullable : 'true',
                            search   : params.term,
                            page     : params.page || 1
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            })
            .on("select2:select", function() {
                let _data = $(this).select2('data')[0];
            });

            var driver_id_select2 = $('#driver_id_select').select2({
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('ShippmentsController@driversSelect') }}';
                    },
                    data: function (params) {
                        var query = {
                            search   : params.term,
                            page     : params.page || 1
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            })
            .on("select2:select", function() {
                let _data = $(this).select2('data')[0];
            });

            const postDriverAndWaybill = function(event) {
                BS.AJAX_FORM(event, {
                    200: function (data) {
                        // datatable.ajax.reload();
                        supplierWaybillAndDriverApp.reload();
                        supplierWaybillAndDriverViewApp.reload();
                    }
                });
            }
        </script>
    @endif

    @if(auth()->check() && auth()->user()->type == 'TRANSPORTER'  && $model->is_rfq == 'YES' && $matchedRfqTransportar)
        <script>
            const supplierRfqApproveApp = new Vue({
                el: '#supplier-rfq-approve-app',
                data: {
                    mode                : 'new',
                    model               : '{{ $model->id }}',
                    is_loading          : false,
                    original_pricing_item : {
                        id                            : null,
                        price                         : 0,
                        pre_additional_location_price : 0,
                        type                          : '{{ $model->type }}',
                    },
                    original_shippment_pricing : {
                        price                         : {{ $model->price }},
                        pre_additional_location_price : {{ $model->pre_additional_location_price }},
                        total_price                   : {{ $model->total_price }},
                        locations_count               : {{ $model->offloadingLocations->count() > 0 ? $model->offloadingLocations->count() - 1 : 0 }},
                    },
                    new_pricing_item : {
                        price                         : 0,
                        pre_additional_location_price : 0,
                    },
                    new_shippment_pricing : {
                        price                         : {{ $model->price }},
                        pre_additional_location_price : {{ $model->pre_additional_location_price }},
                        total_price                   : {{ $model->total_price }},
                        locations_count               : {{ $model->offloadingLocations->count() > 0 ? $model->offloadingLocations->count() - 1 : 0 }},
                    },
                },
                methods: {
                    changeMode: function(mode) {
                        this.mode = mode;
                    },
                    fetchContractPrice: function() {
                        const vm = this;
                        vm.is_loading = true;
                        $.get('{{ route('ShippmentsController@pricingItem') }}', {
                            model : vm.model,
                        }, function(data) {
                            if (data['redirect_url'] || data['redirect']) {
                                window.location = data['redirect_url'] || data['redirect'];
                                return;
                            }
                            if (data['toastr']) {
                                NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                            }

                            if (data['pricing']) {
                                vm.original_pricing_item = {
                                    id                            : data['pricing'].id,
                                    price                         : data['pricing'].contract_instance_pricing.price,
                                    pre_additional_location_price : data['pricing'].contract_instance_pricing.pre_additional_location_price,
                                    type                          : data['pricing'].contract_instance_pricing.type,
                                };
                                vm.new_pricing_item = {
                                    price                         : data['pricing'].contract_instance_pricing.price,
                                    pre_additional_location_price : data['pricing'].contract_instance_pricing.pre_additional_location_price,
                                };
                            }

                            vm.is_loading = false;
                        })
                    },
                }
            });
        </script>
    @endif
@endpush