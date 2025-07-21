{{--
Updated: Added Flasher JS integration
- Added flasher.min.js for notification functionality
- Fixed: Moved JavaScript files from CSS include and proper loading order
--}}

<!-- Popper JS -->
<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Choices JS -->
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<!-- Main Theme Js -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Defaultmenu JS -->
<script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

<!-- Node Waves JS-->
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Sticky JS -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- Simplebar JS -->
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/simplebar.js') }}"></script>

<!-- Color Picker JS -->
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- Custom-Switcher JS -->
{{-- <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script> --}}

<!-- Custom JS -->
{{-- <script src="{{ asset('assets/js/custom.js') }}"></script> --}}

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

<!-- Flasher JS -->
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/js/gallery.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>



<script>
    $(document).ready(function() {
        // Initialize data-table if it exists
        // Fixed DataTable initialization: set 'order' to an empty array to disable initial ordering
        if ($('#data-table').length) {
            $('#data-table').DataTable({
                scrollX: true,
                ordering: false
            });
        }

        // Initialize budget-items-table if it exists
        if ($('#budget-items-table').length) {
            $('#budget-items-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize action-plans-table if it exists
        if ($('#action-plans-table').length) {
            $('#action-plans-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize equipment-table if it exists
        if ($('#equipment-table').length) {
            $('#equipment-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize objectives-table if it exists with disabled search, pagination, and info display
        if ($('#objectives-table').length) {
            $('#objectives-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }
    });
</script>