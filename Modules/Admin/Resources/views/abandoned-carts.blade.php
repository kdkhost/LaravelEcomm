@extends('admin::layouts.master')
@section('title', 'Carrinhos abandonados')
@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Carrinhos abandonados</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total abandonado</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_abandoned'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Convertidos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taxa de recuperação</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['recovery_rate'] ?? 0 }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Receita total</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ format_currency((float) ($stats['total_revenue'] ?? 0)) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abandoned Carts Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de carrinhos abandonados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Total de itens</th>
                                <th>Valor total</th>
                                <th>Última atividade</th>
                                <th>E-mails enviados</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abandonedCarts as $cart)
                                <tr>
                                    <td>{{ $cart->id }}</td>
                                    <td>
                                        @if($cart->user)
                                            {{ $cart->user->name }}
                                        @else
                                            <span class="text-muted">Visitante</span>
                                        @endif
                                    </td>
                                    <td>{{ $cart->email ?? 'N/A' }}</td>
                                    <td>{{ $cart->total_items }}</td>
                                    <td>{{ format_currency((float) ($cart->total_amount)) }}</td>
                                    <td>{{ $cart->last_activity->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $emailsSent = 0;
                                            if ($cart->first_email_sent) $emailsSent++;
                                            if ($cart->second_email_sent) $emailsSent++;
                                            if ($cart->third_email_sent) $emailsSent++;
                                        @endphp
                                        {{ $emailsSent }}/3
                                    </td>
                                    <td>
                                        @if($cart->converted)
                                            <span class="badge badge-success">Convertido</span>
                                        @else
                                            <span class="badge badge-warning">Abandonado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewCartDetails({{ $cart->id }})">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum carrinho abandonado encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $abandonedCarts->links('pagination::admin-bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Details Modal -->
    <div class="modal fade" id="cartDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cartDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartDetailsModalLabel">Detalhes do carrinho</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cartDetailsContent">
                    <!-- Detalhes do carrinho carregados por AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function viewCartDetails(cartId) {
        // Load cart details via AJAX
        fetch(`/admin/analytics/abandoned-carts/${cartId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cart = data.data;
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informações do carrinho</h6>
                                <p><strong>Total de itens:</strong> ${cart.total_items}</p>
                                <p><strong>Valor total:</strong> ${formatCurrency(cart.total_amount)}</p>
                                <p><strong>Última atividade:</strong> ${cart.last_activity}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Status dos e-mails</h6>
                                <p><strong>Primeiro e-mail:</strong> ${cart.first_email_sent || 'Não enviado'}</p>
                                <p><strong>Segundo e-mail:</strong> ${cart.second_email_sent || 'Não enviado'}</p>
                                <p><strong>Terceiro e-mail:</strong> ${cart.third_email_sent || 'Não enviado'}</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Itens do carrinho</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (cart.cart_data && Array.isArray(cart.cart_data)) {
                        cart.cart_data.forEach(item => {
                            html += `
                                <tr>
                                    <td>Produto ID: ${item.product_id || 'N/A'}</td>
                                    <td>${item.quantity || 0}</td>
                                    <td>${formatCurrency(item.price || 0)}</td>
                                    <td>${formatCurrency(item.amount || 0)}</td>
                                </tr>
                            `;
                        });
                    }

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    document.getElementById('cartDetailsContent').innerHTML = html;
                    $('#cartDetailsModal').modal('show');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar detalhes do carrinho:', error);
                alert('Erro ao carregar detalhes do carrinho');
            });
    }

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }
</script>
@endpush

