<div class="box">
    @if ($model->attachment_1)
        <div class="box-info">
            <div class="label">
                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" alt="">
                <span class="name">{{__('contenttypes::inputs.attachment_1.label')}}</span>
            </div>
            <div class="info">
                <a href="{{ app('GraphManager')->src('original', 'files', $model->attachment_1->file_path) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                    <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                </a>
            </div>
        </div>
    @endif
    @if ($model->attachment_2)
        <div class="box-info">
            <div class="label">
                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" alt="">
                <span class="name">{{__('contenttypes::inputs.attachment_2.label')}}</span>
            </div>
            <div class="info">
                <a href="{{ app('GraphManager')->src('original', 'files', $model->attachment_2->file_path) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                    <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                </a>
            </div>
        </div>
    @endif
    @if ($model->attachment_3)
        <div class="box-info">
            <div class="label">
                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" alt="">
                <span class="name">{{__('contenttypes::inputs.attachment_3.label')}}</span>
            </div>
            <div class="info">
                <a href="{{ app('GraphManager')->src('original', 'files', $model->attachment_3->file_path) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                    <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                </a>
            </div>
        </div>
    @endif
    @if ($model->attachment_4)
        <div class="box-info">
            <div class="label">
                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" alt="">
                <span class="name">{{__('contenttypes::inputs.attachment_4.label')}}</span>
            </div>
            <div class="info">
                <a href="{{ app('GraphManager')->src('original', 'files', $model->attachment_4->file_path) }}" class="btn btn-sm btn-success btn-block" target="_blank">
                    <i class="fas fa-external-link-alt" style="margin-left: 5px; margin-right: 5px;"></i>
                    <span>{{ __('contenttypes::inputs.attachments.view_file') }}</span>
                </a>
            </div>
        </div>
    @endif
    <div class="box-info">
        <div class="label">
            <img src="{{ asset('images/icons/number_of_pallets.svg') }}" alt="">
            <span class="name">{{__('contenttypes::inputs.waybill.label')}}</span>
        </div>
        <div class="info">
            @if ($model->WAYBILL)
                <a href="{{ app('GraphManager')->src('original', 'files', $model->WAYBILL->file_path) }}" class="btn btn-sm btn-success btn-block" target="_blank">
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
</div>