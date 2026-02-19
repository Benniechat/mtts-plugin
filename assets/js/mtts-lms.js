document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.mtts-sidebar');
    const toggleBtn = document.querySelector('.mtts-mobile-toggle');
    const closeBtn = document.querySelector('.mtts-sidebar-close');
    const overlay = document.createElement('div');
    overlay.className = 'mtts-sidebar-overlay';
    
    const wrapper = document.querySelector('.mtts-dashboard-wrapper');
    if (wrapper) {
        wrapper.appendChild(overlay);
    }

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // Close sidebar when clicking on a nav link (for mobile)
    const navLinks = document.querySelectorAll('.mtts-nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 991) {
                toggleSidebar();
            }
        });
    });
});
