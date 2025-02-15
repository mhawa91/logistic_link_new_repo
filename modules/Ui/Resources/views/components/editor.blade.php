@php
    $OPTIONS = array_merge([
        'id'           => null,
        'name'         => '',
        'label'        => '',
        'placeholder'  => '',
        'description'  => '',
        'view'         => 'INLINE', //INLINE | DEFAULT | TABLE
        'value'        => '',
        'attributes'   => [],
        'class'        => '',
        'rows'         => 5,
    ], isset($options) ? $options : []);
    $OPTIONS['id'] = $OPTIONS['id'] ? $OPTIONS['id'] : str_replace(['[', ']', '.'], '', $OPTIONS['name']);
    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }
    $BEFORE = isset($before) ? $before : '';
    $AFTER  = isset($after)  ? $after  : '';
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
                    <textarea 
                        name="{{ $OPTIONS['name'] }}" 
                        class="form-control {{ $OPTIONS['class'] }} bs-editor-full" 
                        id="{{ $OPTIONS['id'] }}" 
                        value="{{ $OPTIONS['value'] }}" 
                        placeholder="{{ $OPTIONS['placeholder'] }}" 
                        {!! $ATTRIBUTES !!} rows="{{ $OPTIONS['rows'] }}">{!! $OPTIONS['value'] !!}</textarea>
                    {!! $AFTER !!}
                </div>
            </div>
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'DEFAULT')
    <div class="form-group">
        <label class="form-label" for="{{ $OPTIONS['id'] }}">{!! $OPTIONS['label'] !!}</label>
        <div class="form-control-wrap">
            <textarea 
                name="{{ $OPTIONS['name'] }}" 
                class="form-control {{ $OPTIONS['class'] }} bs-editor-full" 
                id="{{ $OPTIONS['id'] }}" 
                value="{{ $OPTIONS['value'] }}" 
                placeholder="{{ $OPTIONS['placeholder'] }}" 
                {!! $ATTRIBUTES !!} rows="{{ $OPTIONS['rows'] }}">{!! $OPTIONS['value'] !!}</textarea>
            {!! $AFTER !!}
        </div>
    </div>
@elseif ($OPTIONS['view'] == 'TABLE')
    <div class="form-group">
        <div class="form-control-wrap">
            <textarea 
                name="{{ $OPTIONS['name'] }}" 
                class="form-control {{ $OPTIONS['class'] }} bs-editor-full" 
                id="{{ $OPTIONS['id'] }}" 
                value="{{ $OPTIONS['value'] }}" 
                placeholder="{{ $OPTIONS['placeholder'] }}" 
                {!! $ATTRIBUTES !!} rows="{{ $OPTIONS['rows'] }}">{!! $OPTIONS['value'] !!}</textarea>
            {!! $AFTER !!}
        </div>
    </div>
@endif

@push('script')
    <script>
        
    </script> 
@endpush

