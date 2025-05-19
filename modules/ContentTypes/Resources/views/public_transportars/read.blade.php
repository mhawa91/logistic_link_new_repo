@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'xl',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::main.transporter_details'),
        'items' => [
            [
                'title' => __('contenttypes::main.transporters'),
                'link'  => route('PublicTransportarsController@index')
            ],
            [
                'title' => __('contenttypes::main.transporter_details'),
                'link'  => route('PublicTransportarsController@read', ['model' => $model->id])
            ]
        ],
    ]
])
    {{-- <div class="nk-block-head-content">
        <h2 class="nk-block-title fw-normal">
            <a href="#" class="btn btn-custom-1">{{__('contenttypes::main.communicate_to_contract')}}</a>
            <a href="#" class="btn btn-custom-2">{{__('contenttypes::main.add_new_contract')}}</a>
        </h2>
    </div> --}}
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
                                    <h6 class="custom-title">{{__('contenttypes::main.transporter_profile')}}</h6>
                                    <p>{{__('contenttypes::main.details_of_the_transporter_profile')}}</p>
                                </div>
                            </div>
                            <div class="row general-info section-1">
                                <div class="col-lg-12 mb-4">
                                    <p class="m-0 company">{{__('contenttypes::main.company_name')}}</p>
                                    <div class="stars">
                                        <h4>
                                            {{ ucwords($model->company_name) }}
                                        </h4>
                                        <div>
                                            <div class="rating-wrap my-1 d-flex items-center">
                                            <ul class="rating">
                                                <li><em class="icon ni {{$model->reviews_rate >= 1 ? 'ni-star-fill' : 'ni-star'}}"></em></li>
                                                <li><em class="icon ni {{$model->reviews_rate >= 2 ? 'ni-star-fill' : 'ni-star'}}"></em></li>
                                                <li><em class="icon ni {{$model->reviews_rate >= 3 ? 'ni-star-fill' : 'ni-star'}}"></em></li>
                                                <li><em class="icon ni {{$model->reviews_rate >= 4 ? 'ni-star-fill' : 'ni-star'}}"></em></li>
                                                <li><em class="icon ni {{$model->reviews_rate >= 5 ? 'ni-star-fill' : 'ni-star'}}"></em></li>
                                            </ul>
                                            <span class="amount mx-2">{{$model->reviews_rate}} {{ __('contenttypes::inputs.reviews.outof') }} 5</span>
                                            <span class="amount text-danger">({{$model->reviews_count}})</span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="box">
                                        <img src="{{$model->logo}}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 ">
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
                                                        <span class="name">{{__('contenttypes::main.contact_name')}}</span>
                                                    </div>
                                                    <div class="info">
                                                        {{ ucwords($model->address?->primary_contact_name) }}
                                                    </div>
                                                </div>
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
                                                        <span class="name">{{__('contenttypes::orders.email_address')}}</span>
                                                    </div>
                                                    <div class="info" dir="ltr">
                                                        {{ $model->company_email }}
                                                    </div>
                                                </div>
                                                <div class="box-info">
                                                    <div class="label">
                                                        <img src="{{ asset('images/icons/phone.svg') }}" alt="">
                                                        <span class="name">{{__('contenttypes::orders.phone')}}</span>
                                                    </div>
                                                    <div class="info" style="direction: ltr">
                                                        +{{ $model->company_phone_number_ext }}
                                                        {{ $model->company_phone_number }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="box">
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-file text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('contenttypes::inputs.tax_identification_number_string.label')}}</span>
                                            </div>
                                            <div class="info">
                                                @if ($model->tax_identification_number_string)
                                                    <a href="{{ app('GraphManager')->src('original', 'files', $model->tax_identification_number_string) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                                                        <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                                        <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                                                        <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                                        <span>{{ __('contenttypes::inputs.attachments.not_available') }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-file text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('contenttypes::inputs.registration_number.label')}}</span>
                                            </div>
                                            <div class="info">
                                                @if ($model->registration_number)
                                                    <a href="{{ app('GraphManager')->src('original', 'files', $model->registration_number) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                                                        <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                                        <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger btn-block" disabled>
                                                        <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                                                        <span>{{ __('contenttypes::inputs.attachments.not_available') }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <a href="mailto:{{ $model->company_email }}" class="mt-2 btn btn-md btn-block btn-warning btn-block bg-orange">
                                                <i class="fas fa-envelope" style="margin-left: 5px; margin-right: 5px;"></i>
                                                <span>{{ __('contenttypes::inputs.contact_by_email.label') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info section-2">
                                <div class="col-lg-12 mb-2">
                                    <h4>
                                        {{__('contenttypes::main.basic_details')}}
                                    </h4>
                                </div>
                                <div class="col-lg-8 mb-4">
                                    <div class="box">
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-book text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('contenttypes::inputs.registration_type_category_id.label')}}</span>
                                            </div>
                                            <div class="info">
                                                {{ $model->registrationTypeCategory?->__('title') ?? '-----' }}
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-credit-card text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('contenttypes::inputs.registration_number_string.label')}}</span>
                                            </div>
                                            <div class="info" style="direction: ltr">
                                                {{ $model->registration_number_string ?? '-----' }}
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-list text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('contenttypes::inputs.tax_identification_number_string.label')}}</span>
                                            </div>
                                            <div class="info" style="direction: ltr">
                                                {{ $model->tax_identification_number_string ?? '-----' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    {{-- TO DO --}}
                                </div>
                            </div>
                            <div class="row general-info section-3">
                                <div class="col-lg-12 mb-2">
                                    <h4>
                                        {{__('contenttypes::inputs.company_address.label')}}
                                    </h4>
                                </div>
                                <div class="col-lg-8 mb-4">
                                    <div class="box">
                                        <div class="box-info">
                                            <div class="label">
                                                <i class="fas fa-flag text-orange" style="font-size: 20px"></i>
                                                <span class="name">{{__('systemcore::inputs.country.label')}}</span>
                                            </div>
                                            <div class="info">
                                                {{ $model->address?->country_name ?? '-----' }}
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/city.svg') }}" alt="">
                                                <span class="name">{{__('systemcore::inputs.city.label')}}</span>
                                            </div>
                                            <div class="info">
                                                {{ $model->address?->city_name ?? '-----' }}
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/street.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::inputs.street.label')}}</span>
                                            </div>
                                            <div class="info">
                                                {{ $model->address?->street ?? '-----' }}
                                            </div>
                                        </div>
                                        <div class="box-info">
                                            <div class="label">
                                                <img src="{{ asset('images/icons/signs-post.svg') }}" alt="">
                                                <span class="name">{{__('contenttypes::inputs.full_address.label')}}</span>
                                                @if($model->address?->lat && $model->address?->lng)
                                                    <small>
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $model->address?->lat }},{{ $model->address?->lng }}" target="_blank">{{__('contenttypes::orders.view_on_map')}}</a>
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="info" dir="ltr">                                                
                                                {{ $model->address?->full_address ?? '-----' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    {{-- TO DO --}}
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