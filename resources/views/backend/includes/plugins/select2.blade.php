<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2-bootstrap-5-theme.min.css') }}" />
<script src="{{ asset('backend/build/select2-4.1.0/select2.min.js') }}"></script>
<script>
    $(function () {
        $('.select-ele').each(function () {
            $(this).select2({
                theme: "bootstrap-5",
                dropdownParent: $(this).parent(),
            });
        });
    })
</script>
