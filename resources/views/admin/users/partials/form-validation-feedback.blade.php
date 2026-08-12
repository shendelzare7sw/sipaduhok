@if ($errors->any())
    <script>
        window.adminUserValidationErrors = @json($errors->messages());
    </script>
@endif
