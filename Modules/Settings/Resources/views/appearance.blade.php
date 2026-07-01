@extends('admin::layouts.master')

@section('title', 'Aparência do Tema')

@section('content')
<div class="card">
    <h5 class="card-header">Personalização de Cores</h5>
    <div class="card-body">
        <form action="{{ route('settings.appearance.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cor Primária (Admin)</label>
                        <div class="input-group">
                            <input type="color" name="primary_color" value="{{ $themeSettings['primary_color'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="primary_color" value="{{ $themeSettings['primary_color'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cor Secundária</label>
                        <div class="input-group">
                            <input type="color" name="secondary_color" value="{{ $themeSettings['secondary_color'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="secondary_color" value="{{ $themeSettings['secondary_color'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cor da Sidebar</label>
                        <div class="input-group">
                            <input type="color" name="sidebar_bg" value="{{ $themeSettings['sidebar_bg'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="sidebar_bg" value="{{ $themeSettings['sidebar_bg'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cor do Texto da Sidebar</label>
                        <div class="input-group">
                            <input type="color" name="sidebar_text" value="{{ $themeSettings['sidebar_text'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="sidebar_text" value="{{ $themeSettings['sidebar_text'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cor de Destaque (Botões)</label>
                        <div class="input-group">
                            <input type="color" name="accent_color" value="{{ $themeSettings['accent_color'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="accent_color" value="{{ $themeSettings['accent_color'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cor do Cabeçalho</label>
                        <div class="input-group">
                            <input type="color" name="header_bg" value="{{ $themeSettings['header_bg'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="header_bg" value="{{ $themeSettings['header_bg'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cor do Rodapé</label>
                        <div class="input-group">
                            <input type="color" name="footer_bg" value="{{ $themeSettings['footer_bg'] }}" class="form-control form-control-color" style="max-width:60px">
                            <input type="text" name="footer_bg" value="{{ $themeSettings['footer_bg'] }}" class="form-control" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fonte do Site</label>
                        <select name="body_font" class="form-control">
                            <option value="Poppins, sans-serif" {{ $themeSettings['body_font'] == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                            <option value="Open Sans, sans-serif" {{ $themeSettings['body_font'] == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                            <option value="Roboto, sans-serif" {{ $themeSettings['body_font'] == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                            <option value="Lato, sans-serif" {{ $themeSettings['body_font'] == 'Lato, sans-serif' ? 'selected' : '' }}>Lato</option>
                            <option value="Montserrat, sans-serif" {{ $themeSettings['body_font'] == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                            <option value="Nunito, sans-serif" {{ $themeSettings['body_font'] == 'Nunito, sans-serif' ? 'selected' : '' }}>Nunito</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h6>Pré-visualização</h6>
                <div style="display:flex;gap:10px;padding:15px;border:1px solid #dee2e6;border-radius:8px;background:#fff;">
                    <div style="width:60px;height:60px;border-radius:8px;background:{{ $themeSettings['primary_color'] }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">P</div>
                    <div style="width:60px;height:60px;border-radius:8px;background:{{ $themeSettings['accent_color'] }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">D</div>
                    <div style="width:60px;height:60px;border-radius:8px;background:{{ $themeSettings['secondary_color'] }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">S</div>
                    <div style="flex:1;padding-left:15px;">
                        <strong style="font-family:{{ $themeSettings['body_font'] }};">Fonte: {{ explode(',', $themeSettings['body_font'])[0] }}</strong>
                        <p style="font-family:{{ $themeSettings['body_font'] }};margin:5px 0 0;font-size:14px;color:#666;">
                            Exemplo de texto com a fonte selecionada para o site.
                        </p>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">
                <i class="fas fa-save"></i> Salvar Tema
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[type="color"]').forEach(function(picker) {
    picker.addEventListener('input', function() {
        var textInput = this.closest('.input-group').querySelector('input[type="text"]');
        if (textInput) textInput.value = this.value;
    });
});
document.querySelectorAll('input[type="text"][maxlength="7"]').forEach(function(textInput) {
    textInput.addEventListener('input', function() {
        var colorInput = this.closest('.input-group').querySelector('input[type="color"]');
        if (colorInput && /^#[0-9a-fA-F]{6}$/.test(this.value)) {
            colorInput.value = this.value;
        }
    });
});
</script>
@endpush
