@php
    $OPTIONS = array_merge([
        'id'           => null,
        'name'         => '',
        'label'        => '',
        'placeholder'  => '',
        'description'  => '',
        'view'         => 'INLINE', //INLINE | DEFAULT | TABLE
        'type'         => 'text',
        'value'        => '',
        'value_from'   => '',
        'value_to'     => '',
        'step'         => '0.1',
        'disable_step' => false,
        'attributes'   => [],
        'class'        => '',
        'rows'         => 5,
        'file_preview' => false,
        'existing_file_url'  => null,
        'existing_file_name' => null,
    ], isset($options) ? $options : []);
    $OPTIONS['id'] = $OPTIONS['id'] ? $OPTIONS['id'] : str_replace(['[', ']', '.'], '', $OPTIONS['name']);
    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }
    $BEFORE = isset($before) ? $before : '';
    $AFTER  = isset($after)  ? $after  : '';
@endphp
@if ($OPTIONS['type'] == 'hidden')
    <input 
        type="{{ $OPTIONS['type'] }}" 
        name="{{ $OPTIONS['name'] }}" 
        class="form-control {{ $OPTIONS['class'] }}" 
        id="{{ $OPTIONS['id'] }}" 
        value="{{ $OPTIONS['value'] }}" 
        {!! $ATTRIBUTES !!}>
@elseif ($OPTIONS['view'] == 'INLINE')
    <div class="row g-3 align-center">
        <div class="col-lg-4">
            <div class="form-group">
                <label class="form-label" for="{{ $OPTIONS['id'] }}">{!! $OPTIONS['label'] !!}</label>
                @if (! empty($OPTIONS['description']))
                    <span class="form-note">{!! $OPTIONS['description'] !!}</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="form-group">
                <div class="form-control-wrap">
                    @if($OPTIONS['type'] == 'number_range')
                        <div class="input-group"> 
                            <input type="number" class="form-control text-center" 
                                @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                                name="{{ $OPTIONS['name'] }}_from" 
                                placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.from') }})" 
                                class="form-control {{ $OPTIONS['class'] }}" 
                                id="{{ $OPTIONS['id'] }}_from"
                                value="{{ $OPTIONS['value_from'] }}"
                                {!! $ATTRIBUTES !!}>
                            <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                            <input type="number" class="form-control text-center" 
                                @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif
                                name="{{ $OPTIONS['name'] }}_to" 
                                placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.to') }})" 
                                class="form-control {{ $OPTIONS['class'] }}" 
                                id="{{ $OPTIONS['id'] }}_to"
                                value="{{ $OPTIONS['value_to'] }}"
                                {!! $ATTRIBUTES !!}>
                        </div>
                    @elseif ($OPTIONS['type'] == 'daterange')
                        <div class="input-daterange date-picker-range input-group"> 
                            <input type="text" class="form-control" 
                                name="{{ $OPTIONS['name'] }}_from" 
                                placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.from') }})" 
                                class="form-control {{ $OPTIONS['class'] }}" 
                                id="{{ $OPTIONS['id'] }}_from"
                                value="{{ $OPTIONS['value_from'] }}"
                                {!! $ATTRIBUTES !!}>
                            <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                            <input type="text" class="form-control" 
                                name="{{ $OPTIONS['name'] }}_to" 
                                placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.to') }})" 
                                class="form-control {{ $OPTIONS['class'] }}" 
                                id="{{ $OPTIONS['id'] }}_to"
                                value="{{ $OPTIONS['value_to'] }}"
                                {!! $ATTRIBUTES !!}>
                        </div>
                    @elseif ($OPTIONS['type'] == 'textarea')
                        {!! $BEFORE !!}
                        <textarea 
                            name="{{ $OPTIONS['name'] }}" 
                            placeholder="{{ $OPTIONS['placeholder'] }}" 
                            class="form-control {{ $OPTIONS['class'] }}" 
                            id="{{ $OPTIONS['id'] }}" 
                            rows="{{ $OPTIONS['rows'] }}" 
                            {!! $ATTRIBUTES !!}>{!! $OPTIONS['value'] !!}</textarea>
                        {!! $AFTER !!}
                    @elseif($OPTIONS['type'] == 'file')
                        @include('ui::components.file-input', ['options' => $OPTIONS, 'attributes' => $ATTRIBUTES])
                    @elseif($OPTIONS['type'] == 'file_with_preview')
                        <ul class="preview-list g-2">
                            <li class="preview-item">
                                <div class="image-input">
                                    <div class="user-avatar sq xl" style="background: #e8757d00; border: 1px solid #dbdfea;">
                                        <img src="{{ $OPTIONS['value'] ?? $OPTIONS['placeholder'] }}" alt="">
                                        <div class="actions">
                                            <button type="button" class="btn btn-round btn-icon btn-sm btn-secondary select">
                                                <em class="icon ni ni-edit"></em>
                                            </button>
                                            <button type="button" class="btn btn-round btn-icon btn-sm btn-primary remove" data-default="{{ $OPTIONS['placeholder'] }}">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="custom-file" style="display: none;">
                                        <input 
                                            name="{{ $OPTIONS['name'] }}" 
                                            type="file" 
                                            class="custom-file-input {{ $OPTIONS['class'] }}" 
                                            id="{{ $OPTIONS['id'] }}"
                                            {!! $ATTRIBUTES !!}>
                                    </div>
                                    <input 
                                        name="remove_{{ $OPTIONS['name'] }}" 
                                        type="hidden" 
                                        class="custom-file-input-remove"
                                        value="N">
                                </div>
                            </li>
                        </ul>
                    @else
                        {!! $BEFORE !!}
                        <input 
                            type="{{ $OPTIONS['type'] }}" 
                            @if ($OPTIONS['type'] == 'number' && ! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                            name="{{ $OPTIONS['name'] }}" 
                            class="form-control {{ $OPTIONS['class'] }}" 
                            id="{{ $OPTIONS['id'] }}" 
                            value="{{ $OPTIONS['value'] }}" 
                            placeholder="{{ $OPTIONS['placeholder'] }}" 
                            {!! $ATTRIBUTES !!}>
                        {!! $AFTER !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'DEFAULT')
    <div class="form-group">
        <label class="form-label" for="{{ $OPTIONS['id'] }}">{!! $OPTIONS['label'] !!}</label>
        <div class="form-control-wrap">
            @if($OPTIONS['type'] == 'number_range')
                <div class="input-group"> 
                    <input type="number" class="form-control text-center" 
                        @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                        name="{{ $OPTIONS['name'] }}_from" 
                        placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.from') }})" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_from"
                        value="{{ $OPTIONS['value_from'] }}"
                        {!! $ATTRIBUTES !!}>
                    <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                    <input type="number" class="form-control text-center" 
                        @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif
                        name="{{ $OPTIONS['name'] }}_to" 
                        placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.to') }})" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_to"
                        value="{{ $OPTIONS['value_to'] }}"
                        {!! $ATTRIBUTES !!}>
                </div>
            @elseif ($OPTIONS['type'] == 'daterange')
                <div class="input-daterange date-picker-range input-group"> 
                    <input type="text" class="form-control" 
                        name="{{ $OPTIONS['name'] }}_from" 
                        placeholder="{{ $OPTIONS['placeholder'] }}" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_from"
                        value="{{ $OPTIONS['value_from'] }}"
                        {!! $ATTRIBUTES !!}>
                    <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                    <input type="text" class="form-control" name="{{ $OPTIONS['name'] }}_to" 
                        placeholder="{{ $OPTIONS['placeholder'] }}" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_to"
                        value="{{ $OPTIONS['value_to'] }}"
                        {!! $ATTRIBUTES !!}>
                </div>
            @elseif ($OPTIONS['type'] == 'textarea')
                {!! $AFTER !!}
                <textarea 
                    name="{{ $OPTIONS['name'] }}" 
                    placeholder="{{ $OPTIONS['placeholder'] }}" 
                    class="form-control {{ $OPTIONS['class'] }}" 
                    id="{{ $OPTIONS['id'] }}" 
                    rows="{{ $OPTIONS['rows'] }}" 
                    {!! $ATTRIBUTES !!}>{!! $OPTIONS['value'] !!}</textarea>
                {!! $AFTER !!}
            @elseif($OPTIONS['type'] == 'file')
                @include('ui::components.file-input', ['options' => $OPTIONS, 'attributes' => $ATTRIBUTES])
            @elseif($OPTIONS['type'] == 'file_with_preview')
                <ul class="preview-list g-2">
                    <li class="preview-item">
                        <div class="image-input">
                            <div class="user-avatar sq xl" style="background: #e8757d00; border: 1px solid #dbdfea;">
                                <img src="{{ $OPTIONS['value'] ?? $OPTIONS['placeholder'] }}" alt="">
                                <div class="actions">
                                    <button type="button" class="btn btn-round btn-icon btn-sm btn-secondary select">
                                        <em class="icon ni ni-edit"></em>
                                    </button>
                                    <button type="button" class="btn btn-round btn-icon btn-sm btn-primary remove" data-default="{{ $OPTIONS['placeholder'] }}">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </div>
                            </div>
                            <div class="custom-file" style="display: none;">
                                <input 
                                    name="{{ $OPTIONS['name'] }}" 
                                    type="file" 
                                    class="custom-file-input {{ $OPTIONS['class'] }}" 
                                    id="{{ $OPTIONS['id'] }}"
                                    {!! $ATTRIBUTES !!}>
                            </div>
                            <input 
                                name="remove_{{ $OPTIONS['name'] }}" 
                                type="hidden" 
                                class="custom-file-input-remove"
                                value="N">
                        </div>
                    </li>
                </ul>
            @else
                {!! $BEFORE !!}
                <input 
                    type="{{ $OPTIONS['type'] }}" 
                    @if ($OPTIONS['type'] == 'number' && ! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                    name="{{ $OPTIONS['name'] }}" 
                    class="form-control {{ $OPTIONS['class'] }}" 
                    id="{{ $OPTIONS['id'] }}" 
                    value="{{ $OPTIONS['value'] }}" 
                    placeholder="{{ $OPTIONS['placeholder'] }}" 
                    {!! $ATTRIBUTES !!}>
                {!! $AFTER !!}
            @endif
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'TABLE')
    <div class="form-group">
        <div class="form-control-wrap">
            @if($OPTIONS['type'] == 'number_range')
                <div class="input-group"> 
                    <input type="number" class="form-control text-center" 
                        @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                        name="{{ $OPTIONS['name'] }}_from" 
                        placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.from') }})" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_from"
                        value="{{ $OPTIONS['value_from'] }}"
                        {!! $ATTRIBUTES !!}>
                    <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                    <input type="number" class="form-control text-center" 
                        @if (! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif
                        name="{{ $OPTIONS['name'] }}_to" 
                        placeholder="{{ $OPTIONS['placeholder'] }} ({{ __('ui::strings.to') }})" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_to"
                        value="{{ $OPTIONS['value_to'] }}"
                        {!! $ATTRIBUTES !!}>
                </div>
            @elseif($OPTIONS['type'] == 'daterange')
                <div class="input-daterange date-picker-range input-group"> 
                    <input type="text" class="form-control" 
                        name="{{ $OPTIONS['name'] }}_from" 
                        placeholder="{{ $OPTIONS['placeholder'] }}" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_from"
                        value="{{ $OPTIONS['value_from'] }}"
                        {!! $ATTRIBUTES !!}>
                    <div class="input-group-addon">{{ __('ui::strings.to') }}</div> 
                    <input type="text" class="form-control" name="{{ $OPTIONS['name'] }}_to" 
                        placeholder="{{ $OPTIONS['placeholder'] }}" 
                        class="form-control {{ $OPTIONS['class'] }}" 
                        id="{{ $OPTIONS['id'] }}_to"
                        value="{{ $OPTIONS['value_to'] }}"
                        {!! $ATTRIBUTES !!}>
                </div>
            @elseif ($OPTIONS['type'] == 'textarea')
                {!! $BEFORE !!}
                <textarea 
                    name="{{ $OPTIONS['name'] }}" 
                    placeholder="{{ $OPTIONS['placeholder'] }}" 
                    class="form-control {{ $OPTIONS['class'] }}" 
                    id="{{ $OPTIONS['id'] }}" 
                    rows="{{ $OPTIONS['rows'] }}" 
                    {!! $ATTRIBUTES !!}>{!! $OPTIONS['value'] !!}</textarea>
                {!! $AFTER !!}
            @elseif($OPTIONS['type'] == 'file')
                @include('ui::components.file-input', ['options' => $OPTIONS, 'attributes' => $ATTRIBUTES])
            @elseif($OPTIONS['type'] == 'file_with_preview')
                <ul class="preview-list g-2">
                    <li class="preview-item">
                        <div class="image-input">
                            <div class="user-avatar sq xl" style="background: #e8757d00; border: 1px solid #dbdfea;">
                                <img src="{{ $OPTIONS['value'] ?? $OPTIONS['placeholder'] }}" alt="">
                                <div class="actions">
                                    <button type="button" class="btn btn-round btn-icon btn-sm btn-secondary select">
                                        <em class="icon ni ni-edit"></em>
                                    </button>
                                    <button type="button" class="btn btn-round btn-icon btn-sm btn-primary remove" data-default="{{ $OPTIONS['placeholder'] }}">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </div>
                            </div>
                            <div class="custom-file" style="display: none;">
                                <input 
                                    name="{{ $OPTIONS['name'] }}" 
                                    type="file" 
                                    class="custom-file-input {{ $OPTIONS['class'] }}" 
                                    id="{{ $OPTIONS['id'] }}"
                                    {!! $ATTRIBUTES !!}>
                            </div>
                            <input 
                                name="remove_{{ $OPTIONS['name'] }}" 
                                type="hidden" 
                                class="custom-file-input-remove"
                                value="N">
                        </div>
                    </li>
                </ul>
            @else
                {!! $BEFORE !!}
                <input 
                    type="{{ $OPTIONS['type'] }}" 
                    @if ($OPTIONS['type'] == 'number' && ! $OPTIONS['disable_step']) step="{{ $OPTIONS['step'] }}" @endif 
                    name="{{ $OPTIONS['name'] }}" 
                    class="form-control {{ $OPTIONS['class'] }}" 
                    id="{{ $OPTIONS['id'] }}" 
                    value="{{ $OPTIONS['value'] }}" 
                    placeholder="{{ $OPTIONS['placeholder'] }}" 
                    {!! $ATTRIBUTES !!}>
                {!! $AFTER !!}
            @endif
        </div>
    </div>
@endif
