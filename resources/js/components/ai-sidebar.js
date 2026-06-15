(() => {
document.addEventListener('DOMContentLoaded', function() {
// Difficulty hints
const difficultyHints = {
    easy: 'Fakta dasar & hafalan',
    medium: 'Aplikasi konsep & perhitungan',
    hard: 'Analisis & problem solving'
};

// Estimated time based on count
const estimatedTimes = {
    3: '10-15',
    5: '15-20',
    7: '20-25',
    10: '25-30'
};

// Update difficulty hint
document.querySelectorAll('input[name="difficulty"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('difficultyHint').textContent = difficultyHints[this.value];
    });
});

// Update estimated time
document.getElementById('aiQuestionCount').addEventListener('change', function() {
    document.getElementById('estimatedTime').textContent = estimatedTimes[this.value];
});


document.addEventListener('click', function(event) {
    if (!event.target.closest('[data-close-ai-sidebar]')) {
        return;
    }

    if (typeof window.closeAiSidebar === 'function') {
        window.closeAiSidebar();
    }
});
// ==============================
// SIDEBAR RESIZE FUNCTIONALITY
// ==============================

(function() {
    const sidebar = document.getElementById('aiQuestionSidebar');
    const resizeHandle = document.getElementById('aiSidebarResizeHandle');

    if (!sidebar || !resizeHandle) return;

    let isResizing = false;
    let startX = 0;
    let startWidth = 0;

    // Load saved width from localStorage
    const savedWidth = localStorage.getItem('aiSidebarWidth');
    if (savedWidth) {
        const width = parseInt(savedWidth);
        // Validate width is within bounds
        if (width >= 350 && width <= 800) {
            sidebar.style.width = width + 'px';
            // Update hidden position as well
            sidebar.style.right = '-' + width + 'px';
        } else {
            // Invalid width, clear localStorage
            localStorage.removeItem('aiSidebarWidth');
        }
    }

    // Start resize
    resizeHandle.addEventListener('mousedown', function(e) {
        isResizing = true;
        startX = e.clientX;
        startWidth = sidebar.offsetWidth;
        sidebar.classList.add('resizing');

        // Prevent text selection during drag
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'ew-resize';

        e.preventDefault();
    });

    // Perform resize
    document.addEventListener('mousemove', function(e) {
        if (!isResizing) return;

        // Calculate new width (drag left = larger, drag right = smaller)
        const deltaX = startX - e.clientX;
        let newWidth = startWidth + deltaX;

        // Enforce min/max constraints
        const minWidth = 350;
        const maxWidth = 800;
        newWidth = Math.max(minWidth, Math.min(maxWidth, newWidth));

        // Apply new width
        sidebar.style.width = newWidth + 'px';

        // If sidebar is active (visible), keep it at right: 0
        // If sidebar is hidden, update the hidden position
        if (!sidebar.classList.contains('active')) {
            sidebar.style.right = '-' + newWidth + 'px';
        }
    });

    // End resize
    document.addEventListener('mouseup', function() {
        if (isResizing) {
            isResizing = false;
            sidebar.classList.remove('resizing');

            // Restore cursor and text selection
            document.body.style.userSelect = '';
            document.body.style.cursor = '';

            // Save width to localStorage
            const currentWidth = sidebar.offsetWidth;
            localStorage.setItem('aiSidebarWidth', currentWidth);

            // Update hidden position for next open
            if (!sidebar.classList.contains('active')) {
                sidebar.style.right = '-' + currentWidth + 'px';
            }
        }
    });

    // CRITICAL FIX: Wait for ai-question-generator.js to load before overriding
    function initializeResizeOverrides() {
        // Check if window.openAiSidebar is defined (from ai-question-generator.js)
        if (typeof window.openAiSidebar !== 'function') {
            // Not loaded yet, retry after 50ms
            setTimeout(initializeResizeOverrides, 50);
            return;
        }

        // Store original functions
        const originalOpen = window.openAiSidebar;
        const originalClose = window.closeAiSidebar;

        // Override openAiSidebar to respect custom width
        window.openAiSidebar = function() {
            // Ensure sidebar respects saved width BEFORE opening
            const savedWidth = localStorage.getItem('aiSidebarWidth');
            if (savedWidth) {
                sidebar.style.width = savedWidth + 'px';
            }

            // CRITICAL FIX: Reset inline right style to allow CSS transition to work
            sidebar.style.right = '';

            // Call original open function to add 'active' class
            if (originalOpen) originalOpen();
        };

        // Override closeAiSidebar - DO NOT manipulate right position during close
        window.closeAiSidebar = function() {
            // Call original close function first (removes 'active' class, triggers CSS transition)
            if (originalClose) originalClose();

            // After closing animation completes, update hidden position for next open
            setTimeout(() => {
                const currentWidth = sidebar.offsetWidth;
                sidebar.style.right = '-' + currentWidth + 'px';
            }, 300); // Wait for CSS transition to finish (0.3s)
        };
    }

    // Start initialization (will retry until ai-question-generator.js loads)
    initializeResizeOverrides();
})();
});
})();
