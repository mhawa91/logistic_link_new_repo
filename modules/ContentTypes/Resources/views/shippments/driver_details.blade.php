<div class="box">
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.driver_name.label')}}</span>
        </div>
        <div class="info">
            {{ $driver ? ucfirst($driver->name) : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/customer_name.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.driver_id_number.label')}}</span>
        </div>
        <div class="info">
            {{ $driver ? strtoupper($driver->id_number) : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/address.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.driver_nationality_country_id.label')}}</span>
        </div>
        <div class="info" style="direction: ltr">
            {{ $driver && $driver->nationality ? $driver->nationality->name_ar . ' / ' . $driver->nationality->name_en : '----' }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/private_notes.svg') }}" alt="">
            <span class="name">{{ __('contenttypes::inputs.car_information.label') }}</span>
        </div>
        <div class="info" style="white-space: pre-line">
            {{ $driver && $driver->car_information ? $driver->car_information : '----' }}
        </div>
    </div>
</div>
