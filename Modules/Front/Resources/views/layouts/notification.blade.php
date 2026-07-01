<div id="flash-messages"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-info="{{ session('info') }}"
     data-warning="{{ session('warning') }}"
     data-errors="{{ isset($errors) && $errors->any() ? json_encode($errors->all()) : '' }}">
</div>