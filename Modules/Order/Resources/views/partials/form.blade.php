<form method="POST"
      action="{{ route($order->exists ? 'orders.update' : 'orders.store', $order->exists ? $order->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($order->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="status">@lang('partials.status')</label>
        <select name="status" class="form-control">
            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendente</option>
            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Em processamento</option>
            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Enviado</option>
            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregue</option>
            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
    </div>

    <div class="form-group">
        <label for="tracking_number">Codigo de rastreio</label>
        <input type="text" name="tracking_number" id="tracking_number" class="form-control"
               value="{{ $order->tracking_number ?? '' }}" placeholder="Informe o codigo de rastreio">
    </div>

    <div class="form-group">
        <label for="tracking_carrier">Transportadora</label>
        <input type="text" name="tracking_carrier" id="tracking_carrier" class="form-control"
               value="{{ $order->tracking_carrier ?? '' }}" placeholder="Ex.: Correios, Jadlog, Azul Cargo">
    </div>

    <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
</form>
