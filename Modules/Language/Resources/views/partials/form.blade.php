@php $language ??= new \Modules\Language\Models\Language; @endphp
<form class="form-horizontal" method="POST"
      action="{{ route($language->exists ? 'admin.languages.update' : 'admin.languages.store', $language->exists ? $language->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($language->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="code" class="col-form-label">@lang('language::messages.code') <span class="text-danger">*</span></label>
        <input id="code" type="text" name="code" placeholder="pt" maxlength="2"
               value="{{ old('code', $language->code ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="name" class="col-form-label">@lang('language::messages.name') <span class="text-danger">*</span></label>
        <input id="name" type="text" name="name" placeholder="Portuguese"
               value="{{ old('name', $language->name ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="native_name" class="col-form-label">@lang('language::messages.native_name') <span class="text-danger">*</span></label>
        <input id="native_name" type="text" name="native_name" placeholder="Português"
               value="{{ old('native_name', $language->native_name ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="flag" class="col-form-label">@lang('language::messages.flag')</label>
        <input id="flag" type="text" name="flag" placeholder="🇧🇷" maxlength="10"
               value="{{ old('flag', $language->flag ?? '') }}" class="form-control">
    </div>
    <div class="form-group">
        <label for="direction" class="col-form-label">@lang('language::messages.direction') <span class="text-danger">*</span></label>
        <select name="direction" class="form-control">
            <option value="ltr" {{ old('direction', $language->direction ?? 'ltr') === 'ltr' ? 'selected' : '' }}>@lang('language::messages.ltr')</option>
            <option value="rtl" {{ old('direction', $language->direction ?? 'ltr') === 'rtl' ? 'selected' : '' }}>@lang('language::messages.rtl')</option>
        </select>
    </div>
    <div class="form-group">
        <label for="sort_order" class="col-form-label">@lang('language::messages.sort_order')</label>
        <input id="sort_order" type="number" name="sort_order" min="0"
               value="{{ old('sort_order', $language->sort_order ?? 0) }}" class="form-control">
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                {{ old('is_active', $language->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">@lang('language::messages.is_active')</label>
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1"
                {{ old('is_default', $language->is_default ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_default">@lang('language::messages.is_default')</label>
        </div>
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('language::messages.save')</button>
    </div>
</form>
