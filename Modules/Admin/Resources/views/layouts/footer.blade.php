<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; <a href="https://github.com/KalimeroMK" target="_blank">KalimeroMK</a> 2021</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->


<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="{{ route('login') }}">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="{{asset('backend/js/all.min.js')}}"></script>
<script src="{{asset('backend/js/sweetalert2.min.js')}}"></script>
<script src="{{asset('backend/js/toastr.min.js')}}"></script>

<!-- Fix for missing Echo variable globally -->
<script>
    // Fix for missing Echo variable and prevent jQuery deferred exceptions
    if (typeof window.Echo === 'undefined') {
        window.Echo = {
            channel: function() { return { listen: function() {} }; },
            private: function() { return { listen: function() {} }; },
            leave: function() {},
            disconnect: function() {}
        };
    }

    // Prevent jQuery deferred exceptions
    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.message && event.reason.message.includes('Echo')) {
            event.preventDefault();
            console.warn('Echo error suppressed:', event.reason);
        }
    });
</script>

<!-- Page level plugins -->
<script>
    $('#data-table').DataTable({
        "ordering": true,
        "paging": true,
        "pageLength": 25,
        "lengthMenu": [
            [25, 50, 75, 100, -1],
            [25, 50, 75, 100, 'All'],
        ],

    });
</script>
<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        extendedTimeOut: 2000,
    };

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var flash = $('#flash-messages');
        if (flash.length) {
            var success = flash.data('success');
            var error = flash.data('error');
            var successMsg = flash.data('success-msg');
            var info = flash.data('info');
            var warning = flash.data('warning');
            var errors = flash.data('errors');

            if (success) toastr.success(success);
            if (error) toastr.error(error);
            if (successMsg) toastr.success(successMsg);
            if (info) toastr.info(info);
            if (warning) toastr.warning(warning);
            if (errors) {
                try {
                    var errs = typeof errors === 'string' ? JSON.parse(errors) : errors;
                    if (Array.isArray(errs)) {
                        errs.forEach(function(e) { toastr.error(e); });
                    }
                } catch (e) {}
            }
        }

        $('.dltBtn').click(function (e) {
            const form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e74a3b",
                cancelButtonColor: "#858796",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('.btn-confirm').click(function (e) {
            e.preventDefault();
            var text = $(this).data('confirm-text') || 'Are you sure?';
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Confirm',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    window.showConfirm = function (title, text, callback) {
        Swal.fire({
            title: title || "Are you sure?",
            text: text || "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e74a3b",
            cancelButtonColor: "#858796",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed && callback) callback();
        });
    };

    window.showAlert = function (title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon || 'info',
            confirmButtonText: 'OK'
        });
    };
</script>
@stack('scripts')
