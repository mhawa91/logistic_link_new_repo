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
        'select_all'        => false,
        'sync_from'         => null,
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
    if ($OPTIONS['select_all'] && $OPTIONS['multiple']) {
        $ATTRIBUTES .= ' data-select-all="true"';
        $ATTRIBUTES .= ' data-select-all-label="' . e(__('ui::strings.select_all')) . '"';
        $ATTRIBUTES .= ' data-deselect-all-label="' . e(__('ui::strings.deselect_all')) . '"';
    }
    if (! empty($OPTIONS['sync_from']) && $OPTIONS['multiple']) {
        $ATTRIBUTES .= ' data-sync-from="' . e($OPTIONS['sync_from']) . '"';
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

@once
    @push('script')
        <script>
            (function($) {
                function select2Utils() {
                    if (!$.fn.select2 || !$.fn.select2.amd) {
                        return null;
                    }

                    return $.fn.select2.amd.require('select2/utils');
                }

                function selectResultData(element) {
                    const utils = select2Utils();
                    return utils && utils.GetData
                        ? utils.GetData(element, 'data')
                        : $(element).data('data');
                }

                function storeOptionData(option, data) {
                    const utils = select2Utils();

                    if (utils && utils.StoreData) {
                        utils.StoreData(option, 'data', data);
                        return;
                    }

                    $(option).data('data', data);
                }

                function appendSelectedOption($select, item) {
                    let $option = $select.find('option').filter(function() {
                        return String(this.value) === String(item.id);
                    }).first();

                    if ($option.length === 0) {
                        $option = $(new Option(item.text, item.id, true, true));
                        $select.append($option);
                    }

                    $option.prop('selected', true);
                    storeOptionData($option[0], item);
                }

                function addSelectAllResult($select, $results) {
                    if ($results.children('.select2-bulk-actions').length > 0) {
                        return;
                    }

                    const $actions = $('<li>', {
                        class: 'select2-results__option select2-bulk-actions',
                        role: 'presentation',
                    });
                    const $selectAll = $('<button>', {
                        type: 'button',
                        class: 'btn btn-sm btn-outline-primary select2-select-all-result',
                        text: $select.attr('data-select-all-label'),
                    });
                    const $deselectAll = $('<button>', {
                        type: 'button',
                        class: 'btn btn-sm btn-outline-danger select2-deselect-all-result',
                        text: $select.attr('data-deselect-all-label'),
                    });

                    $actions.css({
                        display: 'flex',
                        gap: '0.5rem',
                        padding: '0.375rem',
                        cursor: 'default',
                    });
                    $selectAll.add($deselectAll).css({
                        flex: '1 1 50%',
                        'justify-content': 'center',
                    });
                    $selectAll.add($deselectAll).on('mousedown', function(event) {
                        event.preventDefault();
                    });
                    $selectAll.on('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const items = [];
                        $results.children('.select2-results__option[aria-selected]')
                            .not('[aria-disabled="true"]')
                            .each(function() {
                                const item = selectResultData(this);

                                if (item && item.id !== undefined && item.id !== null) {
                                    items.push(item);
                                }
                            });

                        items.forEach(function(item) {
                            appendSelectedOption($select, item);
                        });

                        $select.trigger('change');
                        items.forEach(function(item) {
                            $select.trigger({
                                type: 'select2:select',
                                params: { data: item },
                            });
                        });
                        $select.select2('close');
                    });
                    $deselectAll.on('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const selectedItems = $select.select2('data') || [];
                        $select.val(null).trigger('change');
                        selectedItems.forEach(function(item) {
                            $select.trigger({
                                type: 'select2:unselect',
                                params: { data: item },
                            });
                        });
                        $select.select2('close');
                    });

                    $actions.append($selectAll, $deselectAll);
                    $results.prepend($actions);
                }

                $(document).on('select2:open.selectAll', 'select[data-select-all="true"]', function() {
                    const $select = $(this);
                    const select2 = $select.data('select2');

                    if (!select2 || !select2.results || !select2.results.$results) {
                        return;
                    }

                    const $results = select2.results.$results;
                    addSelectAllResult($select, $results);

                    const observer = new MutationObserver(function() {
                        addSelectAllResult($select, $results);
                    });
                    observer.observe($results[0], { childList: true });

                    $select.one('select2:close.selectAll', function() {
                        observer.disconnect();
                    });
                });

                $(document).on('show.bs.modal.selectSync', '.modal', function() {
                    $(this).find('select[data-sync-from]').each(function() {
                        const $target = $(this);
                        const $source = $($target.attr('data-sync-from'));

                        if ($source.length === 0 || !$source.data('select2')) {
                            return;
                        }

                        const items = $source.select2('data') || [];
                        const selectedIds = [];

                        $target.find('option').prop('selected', false);
                        items.forEach(function(item) {
                            appendSelectedOption($target, item);
                            selectedIds.push(String(item.id));
                        });

                        $target.val(selectedIds).trigger('change');
                        $target.trigger({
                            type: items.length > 0 ? 'select2:select' : 'select2:unselect',
                            params: { data: items[0] || null },
                        });
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endonce
