@php
    $OPTIONS = array_merge([
        'id'           => null,
        'title'        => '',
        'description'  => '',
        'attributes'   => [],
        'class'        => '',
        'size'         => '',
        'fit'          => false,
    ], isset($options) ? $options : []);
    $OPTIONS['id'] = $OPTIONS['id'] ? $OPTIONS['id'] : str_replace(['[', ']', '.'], '', $OPTIONS['name']);
    $ATTRIBUTES    = '';
    foreach ($OPTIONS['attributes'] as $key => $value) {
        $ATTRIBUTES .= ' ' . $key . '="' . $value . '"';
    }
@endphp
<div class="modal fade {!! $OPTIONS['class'] !!}" id="{!! $OPTIONS['id'] !!}" {!! $ATTRIBUTES !!}>
    <div class="modal-dialog modal-{!! $OPTIONS['size'] !!}" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 0.5rem; padding-top: 0.8rem; background-color: #e5e9f2">
                <h5 class="modal-title" style="color: #737579; font-size: 15px; padding-top: 0.2rem;">{!! $OPTIONS['title'] !!}</h5>
                <a href="javascript:;" class="close" data-dismiss="modal" aria-label="Close" style="padding: 1.25rem 1.25rem;">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body" {!! $OPTIONS['fit'] ? 'style="padding: 0"' : '' !!}>
                {!! $slot !!}
            </div>
            @if (isset($footer))
                <div class="modal-footer bg-light" style="padding: 0.5rem 1.5rem;">
                    {!! $footer !!}
                </div>
            @endif
        </div>
    </div>
</div>