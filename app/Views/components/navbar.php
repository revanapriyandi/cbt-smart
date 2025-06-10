<!-- Top Navbar -->
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="flex items-center justify-between px-3 sm:px-4 lg:px-6 py-3">
        <div class="flex items-center space-x-4">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 lg:hidden transition-colors duration-200">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <!-- Desktop sidebar toggle -->
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden lg:block p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <!-- Page Title -->
            <div class="hidden sm:block">
                <h1 class="text-lg font-semibold text-gray-900">
                    <?= $title ?? 'Dashboard' ?>
                </h1>
            </div>
        </div>

        <!-- User dropdown -->
        <?= $this->include('components/navbar/user_dropdown') ?>
    </div>
</header>