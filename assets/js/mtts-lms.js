document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.lms-sidebar-column');
    const trigger = document.getElementById('lmsSideTrigger');
    
    if (!sidebar || !trigger) return;

    // Create Overlay
    const overlay = document.createElement('div');
    overlay.className = 'lms-drawer-overlay';
    document.querySelector('.mtts-lms-wrapper').appendChild(overlay);

    function toggleDrawer() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('lms-no-scroll');
    }

    trigger.addEventListener('click', toggleDrawer);
    overlay.addEventListener('click', toggleDrawer);

    // Close when clicking nav links on mobile
    const navLinks = sidebar.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                toggleDrawer();
            }
        });
    });
});
