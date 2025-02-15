@php
    $OPTIONS = array_merge([
        'id'                => null,
        'view'              => 'INLINE', //INLINE | DEFAULT | TABLE
        'action'            => '#',
        'method'            => 'POST',
        'attributes'        => [],
        'class'             => '',
    ], isset($options) ? $options : []);
    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }
@endphp

<form 
    action="{!! $OPTIONS['action'] !!}" 
    method="{!! $OPTIONS['method'] !!}" 
    class="gy-3 {{ $OPTIONS['class'] }}" 
    {!! $ATTRIBUTES !!} 
    @if($OPTIONS['id']) id="{{ $OPTIONS['id'] }}" @endif>
    @if ($OPTIONS['method'] == 'POST') @csrf @endif

    {!! $slot !!}
</form>