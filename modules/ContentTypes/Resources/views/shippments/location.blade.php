<div class="col-lg-12 shipments-items">
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
        
        @if(! $from->delivered_at && $index == 0 && auth()->check() && auth()->user()->type == 'TRANSPORTER')
            <div class="col-lg-4 mb-4 mt-4 d-flex justify-end">
                @component('ui::components.form', [
                    'options' => [
                        'id'                => 'location-delivery-app' . $from->id,
                        'action'            => route('ShippmentsController@locationDelivery', ['model' => $shippment->id, 'address_id' => $from->id]),
                        'method'            => 'POST',
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
        @php
            $deliveryConfirmation = $to->DELIVERY_CONFIRMATION;
            $deliveryConfirmationModalId = 'delivery-confirmation-modal-' . $to->id;
            $deliveryConfirmationUrl = $deliveryConfirmation
                ? app('GraphManager')->src('original', 'files', $deliveryConfirmation->file_path)
                : null;
            $nextDeliveryLocation = $shippment->offloadingLocations->first(function ($location) {
                return ! $location->delivered_at;
            });
            $isNextDeliveryLocation = $nextDeliveryLocation && $nextDeliveryLocation->id == $to->id;
        @endphp

        @if($from->delivered_at)
            @if(! $to->delivered_at && $isNextDeliveryLocation && auth()->check() && auth()->user()->type == 'TRANSPORTER')
                <div class="col-lg-4 mb-4 mt-4 d-flex justify-end">
                    <button type="button" class="btn btn-sm btn-success px-3 d-inline-flex align-items-center" data-toggle="modal" data-target="#{{ $deliveryConfirmationModalId }}">
                        <em class="icon ni ni-check-circle mr-1"></em>
                        <span>{{ __('contenttypes::inputs.actions.DELIVERED') }}</span>
                    </button>
                </div>
            @elseif($to->delivered_at && ($deliveryConfirmation || $to->delivery_confirmation_notes || (auth()->check() && auth()->user()->type == 'TRANSPORTER')))
                <div class="col-lg-12 delivery-proof-wrapper">
                    <div class="delivery-proof rounded px-3 py-2">
                        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: .75rem;">
                            <div class="d-flex align-items-center flex-grow-1" style="gap: .65rem; min-width: 220px;">
                                <span class="delivery-proof__icon d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                                    <em class="icon ni ni-check-circle"></em>
                                </span>
                                <div style="min-width: 0;">
                                    <h6 class="mb-0">{{ __('contenttypes::inputs.delivery_confirmation.section_title') }}</h6>
                                    @if($to->delivery_confirmation_notes)
                                        <p class="small text-soft mb-0 mt-1" style="white-space: pre-line; overflow-wrap: anywhere;">{{ $to->delivery_confirmation_notes }}</p>
                                    @else
                                        <span class="small text-soft d-block mt-1">{{ __('contenttypes::inputs.delivery_confirmation.no_notes') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center flex-shrink-0" style="gap: .5rem;">
                                @if($deliveryConfirmationUrl)
                                    <a
                                        href="{{ $deliveryConfirmationUrl }}"
                                        class="btn btn-sm btn-custom-1 px-3"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        <em class="icon ni ni-eye mr-1"></em>
                                        <span>{{ __('contenttypes::inputs.delivery_confirmation.view_file') }}</span>
                                    </a>
                                @endif
                                @if(auth()->check() && auth()->user()->type == 'TRANSPORTER')
                                    <button
                                        type="button"
                                        class="btn btn-sm delivery-proof__edit px-3"
                                        data-toggle="modal"
                                        data-target="#{{ $deliveryConfirmationModalId }}">
                                        <em class="icon ni {{ $deliveryConfirmation || $to->delivery_confirmation_notes ? 'ni-edit' : 'ni-upload-cloud' }} mr-1"></em>
                                        <span>{{ $deliveryConfirmation || $to->delivery_confirmation_notes ? __('contenttypes::inputs.delivery_confirmation.edit') : __('contenttypes::inputs.delivery_confirmation.add') }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @if($from->delivered_at && ($to->delivered_at || $isNextDeliveryLocation) && auth()->check() && auth()->user()->type == 'TRANSPORTER')
        @component('ui::components.form', [
            'options' => [
                'id'         => 'delivery-confirmation-form-' . $to->id,
                'action'     => route('ShippmentsController@postDeliveryConfirmation'),
                'method'     => 'POST',
                'class'      => '',
                'attributes' => [
                    'onsubmit'                     => 'BS.AJAX_FORM(event)',
                    'data-keep-loading-on-success' => 'false',
                    'enctype'                      => 'multipart/form-data',
                ],
            ],
        ])
            @component('ui::components.modal', [
                'options' => [
                    'id'    => $deliveryConfirmationModalId,
                    'title' => __('contenttypes::inputs.delivery_confirmation.title'),
                    'size'  => 'md',
                    'fit'   => false,
                ],
            ])
                <input type="hidden" name="model" value="{{ $shippment->id }}">
                <input type="hidden" name="address_id" value="{{ $to->id }}">

                @include('ui::components.input', [
                    'options' => [
                        'view'               => 'DEFAULT',
                        'type'               => 'file',
                        'id'                 => 'delivery_confirmation_file_' . $to->id,
                        'name'               => 'delivery_confirmation_file',
                        'file_preview'       => true,
                        'existing_file_url'  => $deliveryConfirmationUrl,
                        'existing_file_name' => optional($deliveryConfirmation)->file_name,
                        'label'              => __('contenttypes::inputs.delivery_confirmation.file_label'),
                        'description'        => __('contenttypes::inputs.delivery_confirmation.file_description'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'DEFAULT',
                        'type'        => 'textarea',
                        'id'          => 'delivery_confirmation_notes_' . $to->id,
                        'name'        => 'delivery_confirmation_notes',
                        'label'       => __('contenttypes::inputs.delivery_confirmation.notes_label'),
                        'placeholder' => __('contenttypes::inputs.delivery_confirmation.notes_placeholder'),
                        'value'       => $to->delivery_confirmation_notes,
                        'rows'        => 4,
                    ],
                ])

                <x-slot name="footer">
                    <button type="submit" class="btn btn-sm btn-success">
                        <em class="icon ni ni-check"></em>
                        <span>{{ $to->delivered_at ? __('contenttypes::inputs.delivery_confirmation.save') : __('contenttypes::inputs.delivery_confirmation.confirm_delivery') }}</span>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">
                        <span>{{ __('ui::strings.cancel') }}</span>
                    </button>
                </x-slot>
            @endcomponent
        @endcomponent
    @endif
</div>
