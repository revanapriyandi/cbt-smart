<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CBT Smart' ?> - CBT Smart</title> <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- DataTables JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <?= $this->include('components/styles') ?>
</head>

<body class="bg-gray-50" x-data="{ 
    sidebarOpen: false, 
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || false 
}" x-init="
    $watch('sidebarCollapsed', value => {
        localStorage.setItem('sidebarCollapsed', value);
    });
">

    <!-- Sidebar Component -->
    <?= $this->include('components/sidebar') ?>

    <!-- Main Content -->
    <div class="sidebar-transition"
        :class="{ 
            'lg:ml-72': !sidebarCollapsed,
            'lg:ml-16': sidebarCollapsed,
            'ml-0': window.innerWidth < 1024
        }">

        <!-- Top Navbar Component -->
        <?= $this->include('components/navbar') ?>

        <!-- Content Area -->
        <main class="main-content p-3 sm:p-4 lg:p-6">

            <!-- Flash Messages Component -->
            <?= $this->include('components/flash_messages') ?>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>

        </main>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
    </div>

    <!-- Scripts Component -->
    <?= $this->include('components/scripts') ?>

    <!-- Additional Scripts Section -->
    <?= $this->renderSection('scripts') ?>

</body>

</html>