{{--
    Anti-screenshot & Anti-copy protection for sensitive documents.
    Include this partial in views that need content protection.
    Only activates for non-admin roles (orang_tua, siswa).
--}}
@if(auth()->check() && in_array(auth()->user()->role, ['orang_tua', 'siswa']))
<style>
    /* Prevent text selection */
    .protected-content {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Watermark overlay */
    .protected-content::after {
        content: '{{ auth()->user()->name }} - {{ now()->format("d/m/Y H:i") }}';
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 3rem;
        color: rgba(0, 0, 0, 0.04);
        white-space: nowrap;
        pointer-events: none;
        z-index: 9999;
        font-weight: bold;
        letter-spacing: 5px;
    }

    /* Hide content when printing (force use official download) */
    @media print {
        .protected-content {
            display: none !important;
        }
        body::after {
            content: 'Pencetakan rapor tidak diizinkan. Silakan hubungi sekolah untuk mendapatkan salinan resmi.';
            display: block;
            text-align: center;
            font-size: 1.5rem;
            padding: 100px 50px;
            color: #dc3545;
            font-weight: bold;
        }
    }
</style>

<script>
// Disable right-click context menu
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    return false;
});

// Disable common screenshot shortcuts
document.addEventListener('keydown', function(e) {
    // PrintScreen
    if (e.key === 'PrintScreen') {
        e.preventDefault();
        return false;
    }
    // Ctrl+P (Print)
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        return false;
    }
    // Ctrl+S (Save)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        return false;
    }
    // Ctrl+Shift+I (DevTools)
    if (e.ctrlKey && e.shiftKey && e.key === 'I') {
        e.preventDefault();
        return false;
    }
    // F12 (DevTools)
    if (e.key === 'F12') {
        e.preventDefault();
        return false;
    }
});

// Disable drag
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
    return false;
});
</script>
@endif
