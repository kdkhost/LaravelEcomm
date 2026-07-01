@php
    $inputId = $inputId ?? 'admin-upload-'.uniqid();
    $inputName = $inputName ?? 'images[]';
    $label = $label ?? 'Arquivo';
    $multiple = $multiple ?? false;
    $accept = $accept ?? 'image/*';
    $required = $required ?? false;
    $helpText = $helpText ?? null;
    $existingFiles = collect($existingFiles ?? [])->filter(function ($file) {
        return filled(data_get($file, 'url'));
    })->map(function ($file) {
        $url = data_get($file, 'url');
        $name = data_get($file, 'name') ?: basename(parse_url($url, PHP_URL_PATH) ?: $url);
        $type = data_get($file, 'type') ?: 'image';

        return [
            'url' => $url,
            'name' => $name,
            'type' => $type,
        ];
    })->values();
@endphp

<div class="form-group admin-upload-group">
    <label for="{{ $inputId }}" class="col-form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="admin-upload js-admin-dropzone" data-input="#{{ $inputId }}" data-multiple="{{ $multiple ? '1' : '0' }}">
        <input
            type="file"
            class="admin-upload-input"
            id="{{ $inputId }}"
            name="{{ $inputName }}"
            accept="{{ $accept }}"
            {{ $multiple ? 'multiple' : '' }}
            {{ $required ? 'required' : '' }}
        >

        <div class="admin-upload-dropzone">
            <div class="admin-upload-dropzone-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="admin-upload-dropzone-text">
                <strong>Arraste e solte aqui</strong>
                <span>ou clique para selecionar {{ $multiple ? 'arquivos' : 'um arquivo' }}</span>
            </div>
        </div>

        <div class="admin-upload-preview js-admin-upload-preview">
            @foreach($existingFiles as $file)
                <div class="admin-upload-card is-existing" data-existing="1">
                    <div class="admin-upload-card-media">
                        @if(str_starts_with($file['type'], 'image') || preg_match('/\.(png|jpe?g|webp|gif|svg)$/i', $file['url']))
                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}">
                        @else
                            <div class="admin-upload-file-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        @endif
                    </div>
                    <div class="admin-upload-card-meta">
                        <span class="admin-upload-card-name" title="{{ $file['name'] }}">{{ $file['name'] }}</span>
                        <small>Arquivo atual</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
