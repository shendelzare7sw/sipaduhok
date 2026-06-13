import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Sidebar Toggle Logic
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
    const sidebar = document.querySelector('.sidebar-lms');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth > 768) {
                // Desktop: Toggle collapse on body
                body.classList.toggle('sidebar-collapsed');
            } else {
                // Mobile: Toggle active on sidebar
                sidebar.classList.toggle('active');
            }
        });
    }

    // Close sidebar when clicking outside (Mobile only)
    document.addEventListener('click', function (event) {
        if (window.innerWidth <= 768) {
            // Note: check if sidebarToggle is present (it might not be passed if renamed/missing)
            if (sidebarToggle && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
});

