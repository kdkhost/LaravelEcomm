<div id="flash-messages"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-success-msg="{{ Session::get('success_msg') }}"
     data-info="{{ session('info') }}"
     data-warning="{{ session('warning') }}"
     data-errors="{{ $errors->any() ? json_encode($errors->all()) : '' }}">
</div>