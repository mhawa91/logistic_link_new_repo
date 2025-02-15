@php
    $OPTIONS = array_merge([
        'id'                => null,
        'jsSelect2'         => null,
        'name'              => '',
        'label'             => '',
        'placeholder'       => '',
        'description'       => '',
        'view'              => 'INLINE', //INLINE | DEFAULT | TABLE
        'attributes'        => [],
        'class'             => '',
        'value'             => '',
        'search'            => true,
        'multiple'          => false,
        'nullable'          => false,
        'nullable_value'    => null,
        'options'           => [],
        'option_text'       => function($key, $option)                   { return null; },
        'option_value'      => function($key, $option)                   { return null; },
        'option_sub_text'   => function($key, $option)                   { return null; },
        'option_icon'       => function($key, $option)                   { return null; },
        'option_attributes' => function($key, $option)                   { return []; },
        'selected'          => function($options, $key, $option, $value) { return false; },
        'disabled'          => function($options, $key, $option, $value) { return false; },
    ], isset($options) ? $options : []);
    $OPTIONS['id'] = $OPTIONS['id'] ? $OPTIONS['id'] : str_replace(['[', ']', '.'], '', $OPTIONS['name']);
    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }
    $SELECT_OPTIONS = '';
    if ($OPTIONS['nullable'] && ! $OPTIONS['multiple']) {
        $SELECT_OPTIONS .= '
            <option value="'. e($OPTIONS['nullable_value']) .'" 
                selected="">
                '. e($OPTIONS['placeholder']) .'
            </option>
        ';
    }
    foreach ($OPTIONS['options'] as $key => $value) {
        $OPTION_DATA      = [
            'label'       => (string) $OPTIONS['option_text']       ($key, $value),
            'value'       => (string) $OPTIONS['option_value']      ($key, $value),
            'sub_text'    => (string) $OPTIONS['option_sub_text']   ($key, $value),
            'icon'        => (string) $OPTIONS['option_icon']       ($key, $value),
            'attributes'  => (array)  $OPTIONS['option_attributes'] ($key, $value),
            'selected'    => (bool)   $OPTIONS['selected']          ($OPTIONS['options'], $key, $value, $OPTIONS['value']),
            'disabled'    => (bool)   $OPTIONS['disabled']          ($OPTIONS['options'], $key, $value, $OPTIONS['value']),
        ];
        $OPTION_ATTRIBUTES = '';
        foreach ($OPTION_DATA['attributes'] as $okey => $ovalue) {
            $OPTION_ATTRIBUTES .= ' ' . $okey . '="' . $ovalue . '"';
        }
        $SELECT_OPTIONS .= '
            <option value="'. e($OPTION_DATA['value']) .'" 
                '. $OPTION_ATTRIBUTES .' 
                '. ($OPTION_DATA['disabled'] ? ' disabled=""' : '') .'
                '. ($OPTION_DATA['selected'] ? ' selected=""' : '') .'>
                '. e($OPTION_DATA['label']) .'
            </option>
        ';
    }
@endphp
@if ($OPTIONS['view'] == 'INLINE')
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
                    <select 
                        name="{{ $OPTIONS['name'] }}" 
                        id="{{ $OPTIONS['id'] }}"
                        class="{{ $OPTIONS['jsSelect2'] ? $OPTIONS['jsSelect2'] : 'form-select' }} form-control {{ $OPTIONS['class'] }}" 
                        @if (! empty($OPTIONS['placeholder'])) data-placeholder="{{ $OPTIONS['placeholder'] }}" @endif
                        @if ($OPTIONS['multiple']) multiple="multiple" @endif
                        {!! $ATTRIBUTES !!}
                        data-search="{{ $OPTIONS['search'] ? 'on' : 'off' }}">
                        {!! $SELECT_OPTIONS !!}
                    </select>
                </div>
            </div>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'DEFAULT')
    <div class="form-group">
        <label class="form-label" for="{{ $OPTIONS['id'] }}">{!! $OPTIONS['label'] !!}</label>
        <div class="form-control-wrap">
            <select 
                name="{{ $OPTIONS['name'] }}" 
                id="{{ $OPTIONS['id'] }}"
                class="{{ $OPTIONS['jsSelect2'] ? $OPTIONS['jsSelect2'] : 'form-select' }} form-control {{ $OPTIONS['class'] }}" 
                @if ($OPTIONS['multiple']) multiple="multiple" @endif
                {!! $ATTRIBUTES !!}
                data-search="{{ $OPTIONS['search'] ? 'on' : 'off' }}">
                {!! $SELECT_OPTIONS !!}
            </select>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'TABLE')
    <div class="form-group">
        <div class="form-control-wrap">
            <select 
                name="{{ $OPTIONS['name'] }}" 
                id="{{ $OPTIONS['id'] }}"
                class="{{ $OPTIONS['jsSelect2'] ? $OPTIONS['jsSelect2'] : 'form-select' }} form-control {{ $OPTIONS['class'] }}" 
                @if ($OPTIONS['multiple']) multiple="multiple" @endif
                {!! $ATTRIBUTES !!}
                data-search="{{ $OPTIONS['search'] ? 'on' : 'off' }}">
                {!! $SELECT_OPTIONS !!}
            </select>
        </div>
    </div>
@endif
