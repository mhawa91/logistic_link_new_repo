<div class="col-lg-12 mb-4 shipments-items">    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <h4 class="custom-title-2">
                {{ __('contenttypes::strings.shippments.destination') }} #{{ $index + 1 }}
                @if ($to->round_trip == 'YES')
                    <span class="badge badge-md badge-dot badge-success" style="margin-left: 20px; margin-right: 20px;">{{__('contenttypes::orders.round_trip')}}</span>
                @endif
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.primary_contact_person')}}</span>
            <p class="value">
                {{ $to->primary_contact_company_name }}
                <small>({{ $to->primary_contact_name }})</small>
            </p>
        </div>
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/phone.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.phone')}}</span>
            <p class="value" style="direction: ltr">
                {{ $to->primary_contact_phone_number_ext ?? '' }}
                {{ $to->primary_contact_phone_number ?? '' }}
            </p>
        </div>
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.email_address')}}</span>
            <p class="value">
                {{ $to->primary_contact_email ?? '' }}
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/city.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.city')}}</span>
            <p class="value">
                {{ $to->city->{'name_' . app()->getLocale()} ?? '' }}
            </p>
        </div>
        <div class="col-lg-8 mb-4 map">
            <div class="card-title-group align-start gx-3 main-title" style="border: unset;">
                <div class="card-title">
                    <img src="{{ asset('images/icons/address.svg') }}" alt="">
                    <span class="name">{{__('contenttypes::orders.address')}}</span>
                </div>
                <div class="card-tools view-on-map">
                    @if($to->lat && $to->lng)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $to->lat }},{{ $to->lng }}" target="_blank">{{__('contenttypes::orders.view_on_map')}}</a>
                    @endif
                </div>
            </div>
            <p>
                {{ $to->full_address ?? '' }}
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.public_notes')}}</span>
            <p class="value">
                {{ $to->public_notes ?? '----' }}
            </p>
        </div>
        <div class="col-lg-4 mb-4">
            <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.private_notes')}}</span>
            <p class="value">
                {{ $to->private_notes ?? '----' }}
            </p>
        </div>
        
        @if(! $from->delivered_at && $index == 0)
            <div class="col-lg-4 mb-4 mt-4 d-flex justify-end">
                @component('ui::components.form', [
                    'options' => [
                        'id'                => 'location-delivery-app' . $from->id,
                        'action'            => route('ShippmentsController@locationDelivery', ['model' => $shippment->id, 'address_id' => $from->id]),
                        'method'            => 'GET',
                        'class'             => '',
                        'attributes'        => [
                            'onsubmit'                     => 'BS.AJAX_FORM(event)',
                            'data-keep-loading-on-success' => 'false',
                        ],
                    ],
                ])
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-check" style="margin-left: 5px; margin-right: 5px;"></i>
                        <span>{{ __('contenttypes::inputs.actions.PICKEDUP') }}</span>
                    </button>
                @endcomponent
            </div>
        @endif
        @if($from->delivered_at)
            @if(! $to->delivered_at)
                <div class="col-lg-4 mb-4 mt-4 d-flex justify-end">
                    @component('ui::components.form', [
                        'options' => [
                            'id'                => 'location-delivery-app' . $to->id,
                            'action'            => route('ShippmentsController@locationDelivery', ['model' => $shippment->id, 'address_id' => $to->id]),
                            'method'            => 'GET',
                            'class'             => '',
                            'attributes'        => [
                                'onsubmit'                     => 'BS.AJAX_FORM(event)',
                                'data-keep-loading-on-success' => 'false',
                            ],
                        ],
                    ])
                        <button type="submit" class="btn btn-sm btn-success btn-block">
                            <i class="fas fa-check" style="margin-left: 5px; margin-right: 5px;"></i>
                            <span>{{ __('contenttypes::inputs.actions.DELIVERED') }}</span>
                        </button>
                    @endcomponent
                </div>
            @endif
        @endif
    </div>
</div>