<!-- Sidebar -->
<nav class="fixed inset-y-0 left-0 z-50 sidebar-transition bg-gradient-to-br from-primary-500 to-secondary-500 text-white lg:translate-x-0"
    :class="{ 
        '-translate-x-full': !sidebarOpen && window.innerWidth < 1024, 
        'translate-x-0': sidebarOpen || window.innerWidth >= 1024,
        'w-72': !sidebarCollapsed && window.innerWidth >= 1024,
        'w-64': !sidebarCollapsed && window.innerWidth < 1024,
        'w-16': sidebarCollapsed && window.innerWidth >= 1024
    }">

    <!-- Sidebar Header -->
    <?= $this->include('components/sidebar/header') ?>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto sidebar-scroll max-h-screen">
        <?= $this->include('components/sidebar/navigation') ?>
    </nav>

    <!-- Sidebar Footer -->
    <?= $this->include('components/sidebar/footer') ?>
</nav>