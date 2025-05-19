<div class="box">
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.contact_name.label')}}</span>
        </div>
        <div class="info">
            {{ $company ? ucfirst($company->contact_name) : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.company_name.label')}}</span>
        </div>
        <div class="info">
            {{ $company ? ucfirst($company->company_name) : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/email_address.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.email_address')}}</span>
        </div>
        <div class="info" style="direction: ltr">
            {{ $company ? strtolower($company->company_email) : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/phone.svg') }}" alt="">
            <span class="name">{{__('contenttypes::orders.phone')}}</span>
        </div>
        <div class="info" style="direction: ltr">
            @if ($company)
                +{{ $company->company_phone_number_ext ?? '' }}
                {{ $company->company_phone_number     ?? '' }}
            @else
                ----
            @endif
        </div>
    </div>
</div>