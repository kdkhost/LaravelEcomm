<footer class="app-footer">
    <div class="float-end d-none d-sm-inline-block">
        Painel administrativo
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('admin') }}">Rataplam</a>.</strong>
    Todos os direitos reservados.
</footer>

<a class="scroll-to-top rounded" href="#">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Encerrar sessao?</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">Confirme abaixo para sair do painel administrativo.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair</a>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('backend/vendor/adminlte4/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/vendor/adminlte4/adminlte.min.js') }}"></script>
<script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/js/dataTables.searchBuilder.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('backend/js/adminlte4-bridge.js') }}"></script>

<script>
    if (typeof window.Echo === 'undefined') {
        window.Echo = {
            channel: function() { return { listen: function() {} }; },
            private: function() { return { listen: function() {} }; },
            leave: function() {},
            disconnect: function() {}
        };
    }

    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.message && event.reason.message.includes('Echo')) {
            event.preventDefault();
            console.warn('Echo error suppressed:', event.reason);
        }
    });

    window.alert = function (message) {
        return Swal.fire({
            icon: 'info',
            title: 'Aviso',
            text: String(message),
            confirmButtonText: 'OK'
        });
    };
</script>

<script>
    if ($('#data-table').length) {
        $('#data-table').DataTable({
            ordering: true,
            paging: true,
            pageLength: 25,
            lengthMenu: [
                [25, 50, 75, 100, -1],
                [25, 50, 75, 100, 'All'],
            ],
        });
    }
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.dltBtn').click(function (e) {
            const form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Deseja excluir este registro?',
                text: 'Essa acao nao podera ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<script>
    setTimeout(function () {
        $('.alert').slideUp();
    }, 4000);
</script>
@stack('scripts')
