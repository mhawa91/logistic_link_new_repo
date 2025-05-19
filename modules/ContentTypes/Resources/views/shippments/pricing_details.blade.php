<div class="box">
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.price.label')}}</span>
        </div>
        <div class="info" dir="ltr">
            @if ($model->updated_price && $model->updated_price > 0)
                <span class="text-danger">{{ $helper->moneyFormat($model->updated_price, 2)->abs }} SAR</span>
                <em class="icon ni ni-arrow-left"></em>
                <span class="text-success">{{ $helper->moneyFormat($model->price, 2)->abs }} SAR</span>
            @else
                <span class="text-success">{{ $matchedRfqTransportar ? $helper->moneyFormat($matchedRfqTransportar->price, 2)->abs : $helper->moneyFormat($model->price, 2)->abs }} SAR</span>
            @endif
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.pre_additional_location_price.label')}}</span>
        </div>
        <div class="info" dir="ltr">
            @if ($model->updated_pre_additional_location_price && $model->updated_pre_additional_location_price > 0)
                <span class="text-danger">{{ $helper->moneyFormat($model->updated_pre_additional_location_price, 2)->abs }} SAR</span>
                <em class="icon ni ni-arrow-left"></em>
                <span class="text-success">{{ $helper->moneyFormat($model->pre_additional_location_price, 2)->abs }} SAR</span>
            @else
                <span class="text-success">{{ $matchedRfqTransportar ? $helper->moneyFormat($matchedRfqTransportar->pre_additional_location_price, 2)->abs : $helper->moneyFormat($model->pre_additional_location_price, 2)->abs }} SAR</span>
            @endif
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.additional_locations_count.label')}}</span>
        </div>
        <div class="info" dir="ltr">
            {{ ($model->offloadingLocations->count() - 1) }} {{  __('contenttypes::inputs.additional_locations_count.addon') }}
        </div>
    </div>
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/DeclaredLoadValue.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.shippment_total_price.label')}}</span>
        </div>
        <div class="info" dir="ltr">
            @if ($model->updated_total_price && $model->updated_total_price > 0)
                <span class="text-danger">{{ $helper->moneyFormat($model->updated_total_price, 2)->abs }} SAR</span>
                <em class="icon ni ni-arrow-left"></em>
                <span class="text-success">{{ $helper->moneyFormat($model->total_price, 2)->abs }} SAR</span>
            @else
                <span class="text-success">{{ $matchedRfqTransportar ? $helper->moneyFormat($matchedRfqTransportar->total_price, 2)->abs : $helper->moneyFormat($model->total_price, 2)->abs }} SAR</span>
            @endif
        </div>
    </div>
</div>