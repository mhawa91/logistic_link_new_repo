@php
    $OPTIONS = array_merge([
        'id'                => null,
        'name'              => '',
        'label'             => '',
        'placeholder'       => '',
        'description'       => '',
        'view'              => 'INLINE', //INLINE | DEFAULT | TABLE
        'type'              => 'radio',
        'attributes'        => [],
        'class'             => '',
        'value'             => '',
        'options'           => [],
        'option_id'         => function($key, $option)                   { return 'option_' . $key; },
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
    foreach ($OPTIONS['options'] as $key => $value) {
        $OPTION_DATA     = [
            'id'          => (string) $OPTIONS['option_id']         ($key, $value),
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
            <li>
                <div class="custom-control custom-radio '. ($OPTION_DATA['selected'] ? ' checked' : '') .'">
                    <input type="radio" class="custom-control-input" 
                        name="'.  $OPTIONS['name'] .'" 
                        value="'. e($OPTION_DATA['value']) .'"
                        '. $OPTION_ATTRIBUTES .' 
                        id="'. $OPTION_DATA['id'] .'" 
                        '. ($OPTION_DATA['selected'] ? ' checked=""' : '') .' 
                        '. ($OPTION_DATA['disabled'] ? ' disabled=""' : '') .'>
                    <label class="custom-control-label" for="'. $OPTION_DATA['id'] .'">
                        '. e($OPTION_DATA['label']) .'
                        '. ($OPTION_DATA['sub_text'] ? '<br><small class="text-muted">' . $OPTION_DATA['sub_text'] . '</small>' : '') .'
                    </label>
                </div>
            </li>
        ';
    }
@endphp
@if ($OPTIONS['view'] == 'INLINE')
    <div class="row g-3 align-center">
        <div class="col-lg-4">
            <div class="form-group">
                <label class="form-label">{!! $OPTIONS['label'] !!}</label>
                @if (! empty($OPTIONS['description']))
                    <span class="form-note">{!! $OPTIONS['description'] !!}</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="form-group">
                <div class="form-control-wrap">
                    <ul class="custom-control-group g-3 align-center flex-wrap">
                        {!! $SELECT_OPTIONS !!}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'DEFAULT')
    <div class="form-group">
        <label class="form-label">{!! $OPTIONS['label'] !!}</label>
        <div class="form-control-wrap">
            <ul class="custom-control-group g-3 align-center flex-wrap">
                {!! $SELECT_OPTIONS !!}
            </ul>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'TABLE')
    <div class="form-group">
        <div class="form-control-wrap">
            <ul class="custom-control-group g-3 align-center flex-wrap">
                {!! $SELECT_OPTIONS !!}
            </ul>
        </div>
    </div>
@endif
