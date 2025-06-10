<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('[x-data*="show: true"]').each(function() {
                if (Alpine && Alpine.$data && Alpine.$data(this)) {
                    Alpine.$data(this).show = false;
                }
            });
        }, 5000);

        // Auto-close mobile sidebar when clicking on links
        $(document).on('click', 'nav a', function() {
            if (window.innerWidth < 1024) {
                const sidebarComponent = document.querySelector('[x-data]');
                if (sidebarComponent && sidebarComponent.__x) {
                    sidebarComponent.__x.$data.sidebarOpen = false;
                }
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            // Reset mobile sidebar state on desktop
            const sidebarComponent = document.querySelector('[x-data]');
            if (sidebarComponent && sidebarComponent.__x) {
                sidebarComponent.__x.$data.sidebarOpen = false;
            }
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + B to toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            const component = document.querySelector('[x-data]');
            if (component && component.__x) {
                const data = component.__x.$data;
                if (window.innerWidth >= 1024) {
                    data.sidebarCollapsed = !data.sidebarCollapsed;
                } else {
                    data.sidebarOpen = !data.sidebarOpen;
                }
            }
        }

        // Escape key to close mobile sidebar
        if (e.key === 'Escape') {
            const component = document.querySelector('[x-data]');
            if (component && component.__x && window.innerWidth < 1024) {
                component.__x.$data.sidebarOpen = false;
            }
        }
    });

    // Initialize tooltips for collapsed sidebar items
    document.addEventListener('alpine:init', () => {
        Alpine.data('tooltip', () => ({
            show: false,
            text: '',
            init() {
                this.$nextTick(() => {
                    // Tooltip logic if needed
                });
            }
        }));
    });

    // Performance optimization: Debounce resize events
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Handle responsive changes
            const component = document.querySelector('[x-data]');
            if (component && component.__x) {
                const data = component.__x.$data;
                if (window.innerWidth >= 1024 && data.sidebarOpen) {
                    data.sidebarOpen = false;
                }
            }
        }, 250);
    });
</script>