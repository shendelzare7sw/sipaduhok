import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', function () {
    // Disable Popper for the notification dropdown. The notification-bell
    // component positions the dropdown via inline `!important` styles, but
    // Popper writes to .style.top / .style.left on scroll/resize, which
    // strips the `!important` markers (CSSOM behavior) and snaps the
    // dropdown back to Bootstrap's default dropdown-menu-end position -
    // visible as a sudden shrink on scroll under our sticky header.
    // Sneat doesn't hit this because its navbar isn't sticky on mobile.
    const notifTrigger = document.getElementById('notificationDropdown');
    if (notifTrigger) {
        notifTrigger.setAttribute('data-bs-display', 'static');
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;

    function toggleSidebar() {
        if (window.innerWidth > 768) {
            // Desktop: Toggle collapse on body
            body.classList.toggle('sidebar-collapsed');
        } else {
            // Mobile: Toggle active on sidebar and overlay
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // Clean up state on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // If resizing to desktop, remove active class from sidebar and overlay
            // to prevent them from getting stuck in "mobile open" state
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
});

