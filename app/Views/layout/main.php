<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CBT Smart' ?> - CBT Smart</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            500: '#667eea',
                            600: '#5a6fd8',
                            700: '#4c5bc4',
                            900: '#2d3748'
                        },
                        secondary: {
                            500: '#764ba2',
                            600: '#6a4190'
                        }
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Responsive spacing adjustments */
        @media (min-width: 1024px) and (max-width: 1440px) {
            .main-content {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .sidebar-nav-item {
                margin-left: 0.5rem;
                margin-right: 0.5rem;
            }
        }

        /* Extra small laptop screens */
        @media (min-width: 1024px) and (max-width: 1280px) {
            .main-content {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }

        /* Smooth transitions for sidebar */
        .sidebar-transition {
            transition: width 0.3s ease-in-out, margin 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false }"> <!-- Sidebar -->
    <nav class="fixed inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out bg-gradient-to-br from-primary-500 to-secondary-500 text-white lg:translate-x-0"
        :class="{ 
            '-translate-x-full': !sidebarOpen && window.innerWidth < 1024, 
            'w-72': !sidebarCollapsed && window.innerWidth >= 1024,
            'w-64': !sidebarCollapsed && window.innerWidth < 1024,
            'w-16': sidebarCollapsed && window.innerWidth >= 1024
        }">
        <div class="p-4" :class="{ 'px-2': sidebarCollapsed && window.innerWidth >= 1024 }">
            <h4 class="text-xl font-bold mb-6" :class="{ 'text-center': !sidebarCollapsed || window.innerWidth < 1024, 'text-center': sidebarCollapsed && window.innerWidth >= 1024 }">
                <i class="fas fa-graduation-cap" :class="{ 'mr-2': !sidebarCollapsed || window.innerWidth < 1024 }"></i>
                <span x-show="!sidebarCollapsed || window.innerWidth < 1024">CBT Smart</span>
            </h4>
        </div>
        <nav class="mt-6">
            <?php if (session()->get('role') === 'admin'): ?> <a href="/admin/dashboard"
                    class="sidebar-nav-item flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= uri_string() === 'admin/dashboard' ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-tachometer-alt text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Dashboard</span>
                </a> <a href="/admin/users"
                    class="sidebar-nav-item flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= strpos(uri_string(), 'admin/users') === 0 ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-users text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Manajemen User</span>
                </a>
                <a href="/admin/subjects"
                    class="sidebar-nav-item flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= strpos(uri_string(), 'admin/subjects') === 0 ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-book text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Mata Pelajaran</span>
                </a>
                <a href="/admin/exams"
                    class="sidebar-nav-item flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= strpos(uri_string(), 'admin/exams') === 0 ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-clipboard-list text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Ujian</span>
                </a> <?php elseif (session()->get('role') === 'teacher'): ?>
                <a href="/teacher/dashboard"
                    class="flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= uri_string() === 'teacher/dashboard' ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-tachometer-alt text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Dashboard</span>
                </a>
                <a href="/teacher/exams"
                    class="flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= strpos(uri_string(), 'teacher/exams') === 0 ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-clipboard-list text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Ujian Saya</span>
                </a>
            <?php elseif (session()->get('role') === 'student'): ?>
                <a href="/student/dashboard"
                    class="flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= uri_string() === 'student/dashboard' ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-tachometer-alt text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Dashboard</span>
                </a>
                <a href="/student/results"
                    class="flex items-center py-3 mb-2 rounded-lg transition-colors duration-200 hover:bg-white hover:bg-opacity-10 <?= strpos(uri_string(), 'student/results') === 0 ? 'bg-white bg-opacity-20' : '' ?>"
                    :class="{ 
                        'justify-center px-2 mx-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4 mx-3': !sidebarCollapsed || window.innerWidth < 1024
                    }">
                    <i class="fas fa-chart-line text-center" :class="{ 'w-6': !sidebarCollapsed || window.innerWidth < 1024, 'w-5': sidebarCollapsed && window.innerWidth >= 1024 }"></i>
                    <span x-show="!sidebarCollapsed || window.innerWidth < 1024" class="ml-3">Hasil Ujian</span>
                </a>
            <?php endif; ?>
        </nav>
    </nav> <!-- Main Content -->
    <div class="transition-all duration-300 sidebar-transition"
        :class="{ 
            'lg:ml-72': !sidebarCollapsed,
            'lg:ml-16': sidebarCollapsed,
            'ml-0': window.innerWidth < 1024
        }"><!-- Top Navbar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-3 sm:px-4 lg:px-6 py-3">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 lg:hidden">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Desktop sidebar toggle -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <!-- User dropdown -->
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-user-circle mr-2 text-xl"></i>
                        <span class="hidden sm:block"><?= session()->get('full_name') ?></span>
                        <i class="fas fa-chevron-down ml-2 text-sm"></i>
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                        <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-200">
                            <?= ucfirst(session()->get('role')) ?>
                        </div>
                        <a href="/logout" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header> <!-- Content Area -->
        <main class="main-content p-3 sm:p-4 lg:p-6">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-800"><?= session()->getFlashdata('success') ?></span>
                        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-800"><?= session()->getFlashdata('error') ?></span>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-blue-800"><?= session()->getFlashdata('info') ?></span>
                        <button @click="show = false" class="ml-auto text-blue-500 hover:text-blue-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"></div> <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>