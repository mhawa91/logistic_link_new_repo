@php
    $FILE_OPTIONS = $options;
    $FILE_ATTRIBUTES = $attributes ?? '';
    $HAS_EXISTING_FILE = ! empty($FILE_OPTIONS['existing_file_url']);
@endphp

<div
    class="js-file-preview-field d-flex align-items-center"
    style="gap: .5rem;"
    data-existing-file-url="{{ $FILE_OPTIONS['existing_file_url'] }}"
    data-existing-file-name="{{ $FILE_OPTIONS['existing_file_name'] }}">
    <div class="custom-file flex-grow-1">
        <input
            name="{{ $FILE_OPTIONS['name'] }}"
            type="file"
            class="custom-file-input {{ $FILE_OPTIONS['class'] }} {{ $FILE_OPTIONS['file_preview'] ? 'js-file-preview-input' : '' }}"
            id="{{ $FILE_OPTIONS['id'] }}"
            {!! $FILE_ATTRIBUTES !!}>
        <label class="custom-file-label" for="{{ $FILE_OPTIONS['id'] }}">
            {{ $FILE_OPTIONS['value'] ?? $FILE_OPTIONS['placeholder'] }}
        </label>
    </div>

    @if ($FILE_OPTIONS['file_preview'])
        <a
            @if ($HAS_EXISTING_FILE) href="{{ $FILE_OPTIONS['existing_file_url'] }}" @endif
            class="js-file-preview-link btn btn-icon btn-success flex-shrink-0 {{ $HAS_EXISTING_FILE ? '' : 'd-none' }}"
            target="_blank"
            rel="noopener noreferrer"
            title="{{ $FILE_OPTIONS['existing_file_name'] ?: __('ui::strings.preview_file') }}"
            aria-label="{{ __('ui::strings.preview_file') }}">
            <em class="icon ni ni-eye"></em>
        </a>
    @endif
</div>

@once
    @push('script')
        <script>
            document.addEventListener('change', function (event) {
                var input = event.target.closest('.js-file-preview-input');

                if (!input) {
                    return;
                }

                var field = input.closest('.js-file-preview-field');
                var previewLink = field.querySelector('.js-file-preview-link');
                var file = input.files && input.files.length ? input.files[0] : null;

                if (field.dataset.previewObjectUrl) {
                    URL.revokeObjectURL(field.dataset.previewObjectUrl);
                    delete field.dataset.previewObjectUrl;
                }

                if (!file) {
                    if (field.dataset.existingFileUrl) {
                        previewLink.href = field.dataset.existingFileUrl;
                        previewLink.title = field.dataset.existingFileName || '{{ __('ui::strings.preview_file') }}';
                        previewLink.classList.remove('d-none');
                    } else {
                        previewLink.removeAttribute('href');
                        previewLink.classList.add('d-none');
                    }

                    return;
                }

                var objectUrl = URL.createObjectURL(file);
                field.dataset.previewObjectUrl = objectUrl;
                previewLink.href = objectUrl;
                previewLink.title = file.name;
                previewLink.classList.remove('d-none');
            });
        </script>
    @endpush
@endonce
