@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contentmanager::orders.order_details'),
        'items' => [
            [
                'title' => __('contentmanager::orders.orders'),
                'link'  => 'a'            ],
            [
                'title' => __('contentmanager::orders.order_details'),
                'link'  => 'b'
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
                ⚠️ &nbsp;  {{__('contentmanager::orders.caution')}}
            </div>
            <p>
                There is no transporter has been selected and you must either add a new transporter with a contract or send the request as an RFQ.
            </p>
        </div>
    </div>
    <div class="container-fluid mt-4">
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
                                        {{__('contentmanager::orders.browse_all_transports')}}
                                    </div>
                                </div>
                                <p>
                                    {{__('contentmanager::orders.browse_all_transports_desc')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4 order-info">
        <div class="row">
            <div class="col-lg-12">
                <div class="nk-content-inner">
                    <div class="card card-bordered h-100 border-unset-shadow">
                        <div class="card-inner">
                            <div class="card-title-group align-start gx-3 mb-3 main-title">
                                <div class="card-title">
                                    <h6 class="custom-title">{{__('contentmanager::orders.order_details')}}</h6>
                                    <p>{{__('contentmanager::orders.details_of_the_order')}}</p>
                                </div>
                                <div class="card-tools">
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-primary btn-dim d-none d-sm-inline-flex" data-bs-toggle="dropdown"><em class="icon ni ni-download-cloud"></em><span><span class="d-none d-md-inline">Download</span> Report</span></a>
                                        <a href="#" class="btn btn-icon btn-primary btn-dim d-sm-none" data-bs-toggle="dropdown"><em class="icon ni ni-download-cloud"></em></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a href="#"><span>Download Mini Version</span></a></li>
                                                <li><a href="#"><span>Download Full Version</span></a></li>
                                                <li class="divider"></li>
                                                <li><a href="#"><em class="icon ni ni-opt-alt"></em><span>More Options</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.general_information')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/weight.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.weight')}}</span>
                                                <p class="value">20 {{__('contentmanager::orders.ton')}}</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.DeclaredLoadValue')}}</span>
                                                <p class="value">5000 SAR</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/Commodity.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.Commodity')}}</span>
                                                <p class="btns">
                                                    <a href="#" class="btn btn-custom-1">{{__('contentmanager::orders.furniture')}}</a>
                                                    &nbsp;&nbsp;
                                                    <a href="#" class="btn btn-custom-1">{{__('contentmanager::orders.wood')}}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.order_specifications')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/load_size.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.load_size')}}</span>
                                                <p class="value">{{__('contentmanager::orders.pallets')}}</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/wehicle_condition.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.wehicle_condition')}}</span>
                                                <p class="value">{{__('contentmanager::orders.new')}}</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/number_of_trucks.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.number_of_trucks')}}</span>
                                                <p class="value">10 {{__('contentmanager::orders.trucks')}}</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" class="modify-icon-1">
                                                <span class="name">{{__('contentmanager::orders.number_of_pallets')}}</span>
                                                <p class="value">20 {{__('contentmanager::orders.pallets')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-12">
                                    <h4>
                                        {{__('contentmanager::orders.equipment_details')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-4 mb-4">
                                                <img src="{{ asset('images/icons/truck_type.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.truck_type')}}</span>
                                                <p class="value">{{__('contentmanager::orders.refrigerated')}}</p>
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <img src="{{ asset('images/icons/temperature.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.temperature')}}</span>
                                                <p class="value">-5°C</p>
                                            </div>
                                            <div class="col-lg-4 mb-4">
                                                <img src="{{ asset('images/icons/equipment_length.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.equipment_length')}}</span>
                                                <p class="value">10 {{__('contentmanager::orders.meters')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.public_notes')}}
                                    </h4>
                                    <div class="textarea">
                                        <textarea class="form-control form-control-simple" placeholder="{{__('contentmanager::orders.public_notes')}}"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.private_notes')}}
                                    </h4>
                                    <div class="textarea">
                                        <textarea class="form-control form-control-simple" placeholder="{{__('contentmanager::orders.private_notes')}}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.loading_points_section')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.customer_name')}}</span>
                                                <p class="value">ABC</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/source_name.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.source_name')}}</span>
                                                <p class="value">XYZ</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/city.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.city')}}</span>
                                                <p class="value">Aleppo</p>
                                            </div>
                                            <div class="col-lg-12 mb-4 map">
                                                <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                    <div class="card-title">
                                                        <img src="{{ asset('images/icons/address.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.address')}}</span>
                                                    </div>
                                                    <div class="card-tools view-on-map">
                                                        <a class="" href="#">{{__('contentmanager::orders.view_on_map')}}</a>
                                                    </div>
                                                </div>
                                                <p>
                                                    Abdulaziz king street, 342134 near to the New Mall of Kingdom Riyadh, Saudi Arabia
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.loading_contact_details')}}
                                    </h4>
                                    <div class="box">
                                        <div class="col-lg-12 mb-4">
                                            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                            <span class="name">{{__('contentmanager::orders.primary_contact_person')}}</span>
                                            <p class="value">John Doe</p>
                                        </div>
                                        <div class="col-lg-12 mb-4">
                                            <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                            <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                            <p class="value" style="direction: ltr">+966 52 131 22 55</p>
                                        </div>
                                        <div class="col-lg-12 mb-4">
                                            <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                            <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                            <p class="value">info@logistic-link.com</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-12">
                                    <h4>
                                        {{__('contentmanager::orders.offloading_points_section')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 mb-4">
                                                <div class="card-title-group align-start gx-3 mb-3 main-title">
                                                    <div class="card-title">
                                                        <img src="{{ asset('images/icons/loading_type.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.loading_type')}}</span>
                                                        <p class="value">{{__('contentmanager::orders.ctiy_to_city_with_m_locations')}}</p>
                                                    </div>
                                                    <div class="card-tools">
                                                        <a href="#" class="btn btn-custom-1 mt-3">{{__('contentmanager::orders.round_trip')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <div class="progress-container">
                                                    <div class="progress-bar"></div>
                                                    <div class="progress-fill" style="width: 25%;"></div>
                                                    <div class="step-points">
                                                        <div class="step active"><span class="title">Source</span></div>
                                                        <div class="step active"><span class="title">Destination #1</span></div>
                                                        <div class="step"><span class="title">Destination #2</span></div>
                                                        <div class="step"><span class="title">b</span></div>
                                                        <div class="step"><span class="title">b</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 mb-4 shipments-items">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-4">
                                                        <h4 class="custom-title-2">Destination #1</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.primary_contact_person')}}</span>
                                                        <p class="value">John Doe</p>
                                                    </div>
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                                        <p class="value" style="direction: ltr">+966 52 131 22 55</p>
                                                    </div>
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                                        <p class="value">info@logistic-link.com</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/city.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.city')}}</span>
                                                        <p class="value">Aleppo</p>
                                                    </div>
                                                    <div class="col-lg-8 mb-4 map">
                                                        <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                            <div class="card-title">
                                                                <img src="{{ asset('images/icons/address.svg') }}" alt="">
                                                                <span class="name">{{__('contentmanager::orders.address')}}</span>
                                                            </div>
                                                            <div class="card-tools view-on-map">
                                                                <a class="" href="#">{{__('contentmanager::orders.view_on_map')}}</a>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            Abdulaziz king street, 342134 near to the New Mall of Kingdom Riyadh, Saudi Arabia
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 mb-4">
                                                        <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.private_notes')}}</span>
                                                        <p class="value">Abdulaziz king street, 342134 near to the New Mall of Kingdom, Riyadh, Saudi Arabia</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-4 shipments-items">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-4">
                                                        <h4 class="custom-title-2">Destination #1</h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.primary_contact_person')}}</span>
                                                        <p class="value">John Doe</p>
                                                    </div>
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                                        <p class="value" style="direction: ltr">+966 52 131 22 55</p>
                                                    </div>
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                                        <p class="value">info@logistic-link.com</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 mb-4">
                                                        <img src="{{ asset('images/icons/city.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.city')}}</span>
                                                        <p class="value">Aleppo</p>
                                                    </div>
                                                    <div class="col-lg-8 mb-4 map">
                                                        <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                                                            <div class="card-title">
                                                                <img src="{{ asset('images/icons/address.svg') }}" alt="">
                                                                <span class="name">{{__('contentmanager::orders.address')}}</span>
                                                            </div>
                                                            <div class="card-tools view-on-map">
                                                                <a class="" href="#">{{__('contentmanager::orders.view_on_map')}}</a>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            Abdulaziz king street, 342134 near to the New Mall of Kingdom Riyadh, Saudi Arabia
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 mb-4">
                                                        <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.private_notes')}}</span>
                                                        <p class="value">Abdulaziz king street, 342134 near to the New Mall of Kingdom, Riyadh, Saudi Arabia</p>
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
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        
    </script>
@endpush