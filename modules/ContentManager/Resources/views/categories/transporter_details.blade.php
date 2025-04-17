@extends('ui::layouts.main')

@component('ui::components.transporter_breadcrumb', [
    'options' => [
        'title' => __('contentmanager::main.transporter_details'),
        'items' => [
            [
                'title' => __('contentmanager::main.transporters'),
                'link'  => 'a'            ],
            [
                'title' => __('contentmanager::main.transporter_details'),
                'link'  => 'b'
            ]
        ],
    ]
])
    
@endcomponent

@section('content')

<div class="order-details">
    <div class="container-fluid mt-4 order-info">
        <div class="row">
            <div class="col-lg-12">
                <div class="nk-content-inner">
                    <div class="card card-bordered h-100 border-unset-shadow">
                        <div class="card-inner transporter-profile">
                            <div class="card-title-group align-start gx-3 mb-3 main-title">
                                <div class="card-title">
                                    <h6 class="custom-title">{{__('contentmanager::main.transporter_profile')}}</h6>
                                    <p>{{__('contentmanager::main.details_of_the_transporter_profile')}}</p>
                                </div>
                            </div>
                            <div class="row general-info section-1">
                                <div class="col-lg-12 mb-4">
                                    <p class="m-0 company">{{__('contentmanager::main.company_name')}}</p>
                                    <div class="stars">
                                        <h4>
                                            Logistic Link
                                        </h4>
                                        <div>
                                            <img src="{{ asset('images/icons/active_star.svg') }}" alt="">
                                            <img src="{{ asset('images/icons/active_star.svg') }}" alt="">
                                            <img src="{{ asset('images/icons/active_star.svg') }}" alt="">
                                            <img src="{{ asset('images/icons/active_star.svg') }}" alt="">
                                            <img src="{{ asset('images/icons/star.svg') }}" alt="">&nbsp;&nbsp;&nbsp;
                                            <span>4 {{__('contentmanager::main.from')}} 5</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="box">
                                        <img src="{{ asset('images/icons/test.png') }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 ">
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::main.contact_name')}}</span>
                                                    </div>
                                                    <div class="info">
                                                        Mojahid Akil
                                                    </div>
                                                </div>
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                                    </div>
                                                    <div class="info">
                                                        mojahed@beyond.net
                                                    </div>
                                                </div>
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                        <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                                    </div>
                                                    <div class="info" style="direction: ltr">
                                                        +966 52 131 22 55
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    a
                                </div>
                            </div>
                            <div class="row general-info section-2">
                                <div class="col-lg-12 mb-2">
                                    <h4>
                                        {{__('contentmanager::main.basic_details')}}
                                    </h4>
                                </div>
                                <div class="col-lg-7 mb-4">
                                    <div class="box">
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.contact_name')}}</span>
                                            </div>
                                            <div class="info">
                                                Mojahid Akil
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                            </div>
                                            <div class="info">
                                                mojahed@beyond.net
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                            </div>
                                            <div class="info" style="direction: ltr">
                                                +966 52 131 22 55
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    a
                                </div>
                            </div>
                            <div class="row general-info section-3">
                                <div class="col-lg-12 mb-2">
                                    <h4>
                                        {{__('contentmanager::main.basic_details')}}
                                    </h4>
                                </div>
                                <div class="col-lg-7 mb-4">
                                    <div class="box">
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.contact_name')}}</span>
                                            </div>
                                            <div class="info">
                                                Mojahid Akil
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.email_address')}}</span>
                                            </div>
                                            <div class="info">
                                                mojahed@beyond.net
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::orders.phone')}}</span>
                                            </div>
                                            <div class="info" style="direction: ltr">
                                                +966 52 131 22 55
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    a
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