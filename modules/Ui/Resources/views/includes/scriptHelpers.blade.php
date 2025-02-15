<script>
    var BS_CONFIG = {
        invalidFieldCLass     : 'error',
        invalidFieldHintClass : 'invalid',
        loadingSpinner        : '<span class="spinner-border spinner-grow-sm bs-loading-spinner" role="status" aria-hidden="true" role="status" aria-hidden="true"></span>',
        defaultTableCellClass : 'nk-tb-col',
        // lgTableCellClass      : 'nk-tb-col tb-col-lg',
        // mdTableCellClass      : 'nk-tb-col tb-col-md',
        // smTableCellClass      : 'nk-tb-col tb-col-sm',
        // actionsTableCellClass : 'nk-tb-col nk-tb-col-tools',
        lgTableCellClass      : 'nk-tb-col',
        mdTableCellClass      : 'nk-tb-col',
        smTableCellClass      : 'nk-tb-col',
        actionsTableCellClass : 'nk-tb-col nk-tb-col-tools',
    };

    const BS = {};

    BS.CONFIG = function(key, val = null) {
        if (! val) {
            return BS_CONFIG[ key ] || null;
        }
        BS_CONFIG[ key ] = val;
    };

    BS.SHOW_FORM_VALIDATION_ERRORS = function($Form, $Errors = []) {
        var _CLICK_       = null,
            _CLICK_2_     = null,
            _FOCUS_       = null,
            HIDDEN_ERRORS = '';

        $Errors.forEach((input) => {
            var INPUT_NAME  = input['field'].replace(/\.(.+?)(?=\.|$)/g, (m, s) => `[${s}]`),
                _INPUT_     = $Form.find("[name^='"+ INPUT_NAME + "']");

            _INPUT_.addClass( BS.CONFIG('invalidFieldCLass') );
            if (_INPUT_.parent().hasClass('input-group')) {
                _INPUT_.closest('.form-group').append(`<div class="${ BS.CONFIG('invalidFieldHintClass') }" style="display: block">`+ input['message'] +`</div>`);
            }
            else {
                _INPUT_.parent().append(`<div class="${ BS.CONFIG('invalidFieldHintClass') }" style="display: block">`+ input['message'] +`</div>`);
            }

            if (! _CLICK_ && _INPUT_.data('on-validation-error-click')) {
                _CLICK_ = _INPUT_.data('on-validation-error-click');
            }
            else if(! _CLICK_) {
                let _TAB_ = _INPUT_.closest('.tab-pane').first();
                if (_TAB_) {
                    _CLICK_ = $Form.find(`[href='#${_TAB_.attr('id')}']`);

                    let _PARENT_TAB_ = _TAB_.parent().closest('.tab-pane').first();
                    if (_PARENT_TAB_) {
                        _CLICK_2_ = $Form.find(`[href='#${_PARENT_TAB_.attr('id')}']`);
                    }
                }
            }
            if (! _FOCUS_) {
                _FOCUS_ = _INPUT_;
            }
        });
        if (_CLICK_)   { $(_CLICK_).click(); }
        if (_CLICK_2_) { $(_CLICK_2_).click(); }
        if (_FOCUS_)   { _FOCUS_.focus(); }
    };

    BS.RESET_FORM_VALIDATION_ERRORS = function($Form) {
        $Form.find( '.' + BS.CONFIG('invalidFieldCLass') ).removeClass( BS.CONFIG('invalidFieldCLass') );
        $Form.find( '.' + BS.CONFIG('invalidFieldHintClass') ).remove();
    };

    BS.BTN_TOGGLE_LOADING = function($Btn) {
        if ($Btn.hasClass('bs-loading-btn')) {
            $Btn.prop('disabled', false);
            $Btn.removeClass('bs-loading-btn');
            $Btn.find('.bs-loading-spinner').remove();
            return false;
        }
        $Btn.prop('disabled', true);
        $Btn.addClass('bs-loading-btn');
        $Btn.prepend( BS.CONFIG('loadingSpinner') );
        return true;
    };

    BS.AJAX_FORM = function($Event, $Callbacks = {}) {
        tinymce.triggerSave();
        $Event.preventDefault();
        var $Form   = $($Event.target);
            _SUBMIT = $Form.find('[type=submit]'),
            _URL    = $Form.attr('action') || 'javascript:;',
            _METHOD = $Form.attr('method') || 'POST';
            _DATA   = new FormData($Event.target); //_NODE_.serialize(); 
        
        var _OPT = $.extend({
            keepLoadingOnSuccess: false,
        }, $Form.data());

        BS.RESET_FORM_VALIDATION_ERRORS($Form);
        BS.BTN_TOGGLE_LOADING(_SUBMIT);

        $.ajax({
            contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS
            url    : _URL,
            method : _METHOD,
            data   : _DATA,
            statusCode: {
                200: function(data) {

                    if ($Callbacks[ data.code ]) {
                        $Callbacks[ data.code ].apply(data, [$Form, _SUBMIT]);
                        return;
                    }

                    if (data['redirect_url'] || data['redirect']) {
                        window.location = data['redirect_url'] || data['redirect'];
                        return;
                    }

                    if (data['toastr']) {
                        NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                    }

                    if (data.status) {
                        if (! _OPT.keepLoadingOnSuccess) {
                            BS.BTN_TOGGLE_LOADING(_SUBMIT);
                        }
                    }
                    else if(data.code == '422') {
                        BS.SHOW_FORM_VALIDATION_ERRORS($Form, data.errors.invalid_fields);
                        BS.BTN_TOGGLE_LOADING(_SUBMIT);
                    }
                    else {
                        BS.BTN_TOGGLE_LOADING(_SUBMIT);
                    }
                },
                500: function(XHR) {
                    var data = JSON.parse(XHR.responseText);
                    if ($Callbacks['500']) {
                        $Callbacks['500'].apply(data, [$Form, _SUBMIT]);
                        return;
                    }
                }
            },
        })
        .done(function(data) {
            if ($Callbacks['done']) {
                $Callbacks['done'].apply(data, [$Form, _SUBMIT]);
                return;
            }
        })
        .fail(function(XHR) {
            BS.BTN_TOGGLE_LOADING(_SUBMIT);
            var data = JSON.parse(XHR.responseText);
            if ($Callbacks['fail']) {
                $Callbacks['fail'].apply(data, [$Form, _SUBMIT]);
                return;
            }
        })
        .always(function() {
            if ($Callbacks['always']) {
                $Callbacks['always'].apply(null, [$Form, _SUBMIT]);
                return;
            }
        });

        return false;
    };

    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader('platform', 'WEB');
            xhr.setRequestHeader('locale'  , '{{ $_LOCALE_ }}');
        }
    });

    BS.EXTEND = function extend(obj, ext) {
        return $.extend(obj, ext);
    }

    BS.reloadDatatable = function(datatable) {
        datatable.ajax.reload( null, false );
    }

    BS.resetForm = function(btn = null, form = null) {
        if (btn) {
            $( btn ).closest('form').trigger("reset");
            $( btn ).closest('form').find('.form-select').select2();
        }
        else if(form) {
            $( form ).trigger("reset");
            $( form ).find('.form-select').select2();
        }
    }

    BS.DATATABLE = function(elm, opt) {
        if ($(elm).exists()) {
            var auto_responsive = $(elm).data('auto-responsive'), has_export = (typeof(opt.buttons) !== 'undefined' && opt.buttons) ? true : false;
            var btn = (has_export) ? '<"dt-export-buttons d-flex align-center"<"dt-export-title d-none d-md-inline-block">B>' : '', btn_cls = (has_export) ? ' with-export' : '';
            var dom_normal = '<"row justify-between'+btn_cls+'"<"col-6 col-sm-4 text-left"f><"col-5 col-sm-8 text-right"<"datatable-filter"<"d-flex justify-content-end g-2"'+btn+'l>>>><"datatable-wrap"t><"row align-items-center"<"col-6 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-left text-md-right"i>>'; 
            var dom_separate = '<"row justify-between'+btn_cls+'"<"col-6 col-sm-4 text-left"f><"col-5 col-sm-8 text-right"<"datatable-filter"<"d-flex justify-content-end g-2"'+btn+'l>>>><"my-3"t><"row align-items-center"<"col-6 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-left text-md-right"i>>'; 
            var dom = $(elm).hasClass('is-separate') ? dom_separate : dom_normal;
            var def = {
                responsive: true,
                autoWidth: false,
                dom: dom,
                language: dataTableTranslations
            }, 
            attr = (opt) ? BS.EXTEND(def, opt) : def;
            attr =(auto_responsive === false) ?  BS.EXTEND(attr, {responsive: false}) : attr;
            return $(elm).DataTable(attr);
        }
    };

    BS.ajaxConfirm = function(_options_, $Callbacks = {}) {
        _options = BS.EXTEND({
            title             : 'Are you sure?',
            text              : 'Please confirm your action',
            confirmButtonText : 'Confirm',
            cancelButtonText  : 'Cancel',
            icon              : 'warning',
            url               : '#',
            method            : 'POST',
            data              : {},
        }, _options_ || {});
        _options.data = _options_.data || {};
        Swal.fire({
            title             : _options.title,
            html              : _options.text,
            icon              : _options.icon,
            showCancelButton  : true,
            confirmButtonText : _options.confirmButtonText,
            cancelButtonText  : _options.cancelButtonText,
        })
        .then(function (e) {
            if (e.value) {
                if ($Callbacks['confirmed']) {
                    $Callbacks['confirmed'].apply(null);
                }
                $.ajax({
                    url    : _options.url,
                    method : _options.method,
                    data   : _options.data,
                    statusCode: {
                        200: function(data) {
                            if ($Callbacks[ data.code ]) {
                                $Callbacks[ data.code ].apply(data);
                                return;
                            }
                            if (data['redirect_url'] || data['redirect']) {
                                window.location = data['redirect_url'] || data['redirect'];
                                return;
                            }

                            if (data['toastr']) {
                                NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
                            }
                        },
                        500: function(XHR) {
                            var data = JSON.parse(XHR.responseText);
                        }
                    },
                })
                .done(function(data) {
                    if ($Callbacks['done']) {
                        $Callbacks['done'].apply(data);
                        return;
                    }
                })
                .fail(function(XHR) {
                    BS.BTN_TOGGLE_LOADING(_SUBMIT);
                    var data = JSON.parse(XHR.responseText);
                    if ($Callbacks['fail']) {
                        $Callbacks['fail'].apply(data);
                        return;
                    }
                })
                .always(function() {
                    if ($Callbacks['always']) {
                        $Callbacks['always'].apply(null);
                        return;
                    }
                });
            }
            else if ($Callbacks['rejected']) {
                $Callbacks['rejected'].apply(null);
            }
        });
    }

    BS.ajaxDelete = function(_url, model, datatable = null) {
        var $Callbacks = {
            done : function(data) {
                if (datatable) {
                    BS.reloadDatatable(datatable);
                }
            }
        };
        BS.ajaxConfirm({
            title             : '{{ __('ui::strings.confirmations.delete.title') }}',
            text              : '{{ __('ui::strings.confirmations.delete.text') }}',
            confirmButtonText : '{{ __('ui::strings.confirmations.delete.confirm') }}',
            cancelButtonText  : '{{ __('ui::strings.confirmations.delete.cancel') }}',
            url               : _url,
            data              : {
                _token : '{{ csrf_token() }}',
                model  : model,
            }
        }, $Callbacks);
    }

    BS.ajaxRestore = function(_url, model, datatable = null) {
        var $Callbacks = {
            done : function(data) {
                if (datatable) {
                    BS.reloadDatatable(datatable);
                }
            }
        };
        BS.ajaxConfirm({
            title             : '{{ __('ui::strings.confirmations.restore.title') }}',
            text              : '{{ __('ui::strings.confirmations.restore.text') }}',
            confirmButtonText : '{{ __('ui::strings.confirmations.restore.confirm') }}',
            cancelButtonText  : '{{ __('ui::strings.confirmations.restore.cancel') }}',
            url               : _url,
            data              : {
                _token : '{{ csrf_token() }}',
                model  : model,
            }
        }, $Callbacks);
    }
    
    BS.ajaxPermaDelete = function(_url, model, datatable = null) {
        var $Callbacks = {
            done : function(data) {
                if (datatable) {
                    BS.reloadDatatable(datatable);
                }
            }
        };
        BS.ajaxConfirm({
            title             : '{!! __('ui::strings.confirmations.permaDelete.title') !!}',
            text              : '{!! __('ui::strings.confirmations.permaDelete.text') !!}',
            confirmButtonText : '{!! __('ui::strings.confirmations.permaDelete.confirm') !!}',
            cancelButtonText  : '{!! __('ui::strings.confirmations.permaDelete.cancel') !!}',
            url               : _url,
            data              : {
                _token : '{{ csrf_token() }}',
                model  : model,
            }
        }, $Callbacks);
    }

    BS.CALLABLES = {};

    BS.REGISTER  = function(name, CallableClass) {
        BS.CALLABLES[ name ] = CallableClass;
    };

    BS.loadCallable  = function(name) {
        return BS.CALLABLES[ name ] || null;
    };

    $(document).on('click', '.image-input .select', function() {
        $(this).closest('.image-input').find('input[type=file]').first().click();
        $(this).closest('.image-input').find('input.custom-file-input-remove').first().val('N');
    });
    $(document).on('click', '.image-input .remove', function() {
        $(this).closest('.image-input').find('img').first().attr('src', $(this).data('default') || '');
        $(this).closest('.image-input').find('input[type=file]').first().val('');
        $(this).closest('.image-input').find('input.custom-file-input-remove').first().val('Y');
    });
    $(document).on('change', '.image-input input[type=file]', function(event) {
        var imgTag = $(this).closest('.image-input').find('img').first();
        var reader = new FileReader();
        reader.onload = function() {
            imgTag.attr('src', reader.result);
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    tinymce.init({
        selector : 'textarea.bs-editor-full',
        language : '{{ $_LOCALE_ }}',
        plugins  : [
            'advlist autolink link image lists charmap hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
            'table emoticons template paste'
        ],
        toolbar: 
            'undo redo | fontselect | styleselect | fontsizeselect | bold italic underline lineheight | forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image media | ' +
            'fullscreen emoticons | removeformat | code visualaid | searchreplace | emoticons',
        menubar: 'file edit view insert format tools table',
        automatic_uploads: true,
        file_picker_types: 'image',
        /* and here's our custom image picker*/
        // file_picker_callback: function (cb, value, meta) {
        //     var input = document.createElement('input');
        //     input.setAttribute('type', 'file');
        //     input.setAttribute('accept', 'image/*');
        //     input.onchange = function () {
        //         var file = this.files[0];
        //         var reader = new FileReader();
        //         reader.onload = function () {
        //             var id = 'blobid' + (new Date()).getTime();
        //             var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        //             var base64 = reader.result.split(',')[1];
        //             var blobInfo = blobCache.create(id, file, base64);
        //             blobCache.add(blobInfo);
        //             cb(blobInfo.blobUri(), { title: file.name });
        //         };
        //         reader.readAsDataURL(file);
        //     };
        //     input.click();
        // },
        // content_css: 'css/content.css',
        images_upload_url: '{{ route('AdminDashboardController@editorUpload') }}',
        images_upload_handler: function (blobInfo, success, failure, progress) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route('AdminDashboardController@editorUpload') }}');
            xhr.upload.onprogress = function (e) {
                progress(e.loaded / e.total * 100);
            };
            xhr.onload = function() {
                var jsonResponse;
                if (xhr.status === 403) {
                    failure('HTTP Error: ' + xhr.status, { remove: true });
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                jsonResponse = JSON.parse(xhr.responseText);
                if (!jsonResponse || typeof jsonResponse.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(jsonResponse.location);
            };
            xhr.onerror = function () {
                failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', '{{ csrf_token() }}');
            xhr.send(formData);
        }
    });
</script>

@if(session()->has('system_response'))
    <script>
        var data = {!! json_encode(session()->get('system_response')) !!};
        if (data['toastr']) {
            NioApp.Toast('<h5>'+ data['toastr']['title'] +'</h5><p>'+ data['toastr']['description'] +'</p>', data['toastr']['type'], {ui: 'is-dark'});
        }
    </script>
@endif