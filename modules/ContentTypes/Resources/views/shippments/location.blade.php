<div class="col-lg-12 mb-4 shipments-items">
    <div class="row">
        <div class="col-lg-12 mt-4 mb-4">
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
        @if($to->public_notes)
            <div class="col-lg-4 mb-4">
                <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                <span class="name">{{__('contenttypes::orders.public_notes')}}</span>
                <p class="value">{{ $to->public_notes }}</p>
            </div>
        @endif
        @if($to->public_notes)
            <div class="col-lg-8 mb-4">
                <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
                <span class="name">{{__('contenttypes::orders.private_notes')}}</span>
                <p class="value">{{ $to->private_notes }}</p>
            </div>
        @endif
    </div>
</div>