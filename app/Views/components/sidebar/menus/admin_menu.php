<!-- Dashboard -->
<a href="/admin/dashboard"
    class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= uri_string() === 'admin/dashboard' ? 'nav-item-active' : '' ?>"
    :class="{ 
                        'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                        'px-4': !sidebarCollapsed || window.innerWidth < 1024
                    }">
    <i class="fas fa-tachometer-alt text-lg flex-shrink-0"></i>
    <span class="ml-3 font-medium sidebar-transition overflow-hidden"
        x-show="!sidebarCollapsed || window.innerWidth < 1024">
        Dashboard
    </span>
</a>

<!-- User Management -->
<div x-data="{ expanded: <?= strpos(uri_string(), 'admin/users') === 0 ? 'true' : 'false' ?> }" class="mb-2">
    <button @click="!sidebarCollapsed && (expanded = !expanded)"
        class="w-full flex items-center py-3 rounded-lg nav-item-hover <?= strpos(uri_string(), 'admin/users') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
                            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                            'px-4': !sidebarCollapsed || window.innerWidth < 1024
                        }">
        <i class="fas fa-users text-lg flex-shrink-0"></i>
        <div class="flex items-center justify-between w-full ml-3 sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            <span class="font-medium">Manajemen User</span>
            <i class="fas fa-chevron-down text-sm transition-transform duration-200"
                :class="{ 'rotate-180': expanded }"></i>
        </div>
    </button>

    <!-- Submenu -->
    <div x-show="expanded && (!sidebarCollapsed || window.innerWidth < 1024)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1 ml-6 space-y-1">
        <a href="/admin/users"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= uri_string() === 'admin/users' ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-list w-4 mr-3"></i>
            Semua User
        </a>
        <a href="/admin/users/admins"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= uri_string() === 'admin/users/admins' ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-user-shield w-4 mr-3"></i>
            Administrator
        </a>
        <a href="/admin/users/teachers"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= uri_string() === 'admin/users/teachers' ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-chalkboard-teacher w-4 mr-3"></i>
            Guru
        </a>
        <a href="/admin/users/students"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= uri_string() === 'admin/users/students' ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-user-graduate w-4 mr-3"></i>
            Siswa
        </a>
    </div>
</div>

<!-- Academic Management -->
<div x-data="{ expanded: <?= strpos(uri_string(), 'admin/academic') === 0 || strpos(uri_string(), 'admin/subjects') === 0 || strpos(uri_string(), 'admin/classes') === 0 || strpos(uri_string(), 'admin/schedules') === 0 ? 'true' : 'false' ?> }" class="mb-2">
    <button @click="!sidebarCollapsed && (expanded = !expanded)"
        class="w-full flex items-center py-3 rounded-lg nav-item-hover <?= strpos(uri_string(), 'admin/academic') === 0 || strpos(uri_string(), 'admin/subjects') === 0 || strpos(uri_string(), 'admin/classes') === 0 || strpos(uri_string(), 'admin/schedules') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
                            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                            'px-4': !sidebarCollapsed || window.innerWidth < 1024
                        }">
        <i class="fas fa-graduation-cap text-lg flex-shrink-0"></i>
        <div class="flex items-center justify-between w-full ml-3 sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            <span class="font-medium">Akademik</span>
            <i class="fas fa-chevron-down text-sm transition-transform duration-200"
                :class="{ 'rotate-180': expanded }"></i>
        </div>
    </button>

    <div x-show="expanded && (!sidebarCollapsed || window.innerWidth < 1024)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1 ml-6 space-y-1">
        <a href="/admin/subjects"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/subjects') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-book w-4 mr-3"></i>
            Mata Pelajaran
        </a>
        <a href="/admin/classes"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/classes') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-chalkboard w-4 mr-3"></i>
            Kelas
        </a>
        <a href="/admin/academic-years"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/academic-years') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-calendar-alt w-4 mr-3"></i>
            Tahun Ajaran
        </a>
        <a href="/admin/schedules"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/schedules') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-clock w-4 mr-3"></i>
            Jadwal
        </a>
    </div>
</div>

<!-- Exam Management -->
<div x-data="{ expanded: <?= strpos(uri_string(), 'admin/exams') === 0 || strpos(uri_string(), 'admin/question-banks') === 0 || strpos(uri_string(), 'admin/exam-types') === 0 || strpos(uri_string(), 'admin/exam-sessions') === 0 ? 'true' : 'false' ?> }" class="mb-2">
    <button @click="!sidebarCollapsed && (expanded = !expanded)"
        class="w-full flex items-center py-3 rounded-lg nav-item-hover <?= strpos(uri_string(), 'admin/exams') === 0 || strpos(uri_string(), 'admin/question-banks') === 0 || strpos(uri_string(), 'admin/exam-types') === 0 || strpos(uri_string(), 'admin/exam-sessions') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
                            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                            'px-4': !sidebarCollapsed || window.innerWidth < 1024
                        }">
        <i class="fas fa-clipboard-list text-lg flex-shrink-0"></i>
        <div class="flex items-center justify-between w-full ml-3 sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            <span class="font-medium">Ujian</span>
            <i class="fas fa-chevron-down text-sm transition-transform duration-200"
                :class="{ 'rotate-180': expanded }"></i>
        </div>
    </button>

    <div x-show="expanded && (!sidebarCollapsed || window.innerWidth < 1024)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1 ml-6 space-y-1">
        <a href="/admin/exams"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= uri_string() === 'admin/exams' ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-list w-4 mr-3"></i>
            Semua Ujian
        </a>
        <a href="/admin/exam-types"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/exam-types') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-tags w-4 mr-3"></i>
            Jenis Ujian
        </a> <a href="/admin/question-banks"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/question-banks') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-database w-4 mr-3"></i>
            Bank Soal
        </a>
        <a href="/admin/questions"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/questions') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-question-circle w-4 mr-3"></i>
            Kelola Soal
        </a>
        <a href="/admin/exam-sessions"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/exam-sessions') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-play-circle w-4 mr-3"></i>
            Sesi Ujian
        </a>
    </div>
</div>

<!-- Results & Monitoring -->
<div x-data="{ expanded: <?= strpos(uri_string(), 'admin/monitoring') === 0 || strpos(uri_string(), 'admin/results') === 0 || strpos(uri_string(), 'admin/analytics') === 0 || strpos(uri_string(), 'admin/reports') === 0 ? 'true' : 'false' ?> }" class="mb-2">
    <button @click="!sidebarCollapsed && (expanded = !expanded)"
        class="w-full flex items-center py-3 rounded-lg nav-item-hover <?= strpos(uri_string(), 'admin/monitoring') === 0 || strpos(uri_string(), 'admin/results') === 0 || strpos(uri_string(), 'admin/analytics') === 0 || strpos(uri_string(), 'admin/reports') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
                            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                            'px-4': !sidebarCollapsed || window.innerWidth < 1024
                        }">
        <i class="fas fa-chart-bar text-lg flex-shrink-0"></i>
        <div class="flex items-center justify-between w-full ml-3 sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            <span class="font-medium">Monitoring & Laporan</span>
            <i class="fas fa-chevron-down text-sm transition-transform duration-200"
                :class="{ 'rotate-180': expanded }"></i>
        </div>
    </button>

    <div x-show="expanded && (!sidebarCollapsed || window.innerWidth < 1024)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1 ml-6 space-y-1">
        <a href="/admin/monitoring/live"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/monitoring/live') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-eye w-4 mr-3"></i>
            Monitor Live
        </a>
        <a href="/admin/results"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/results') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-poll w-4 mr-3"></i>
            Hasil Ujian
        </a>
        <a href="/admin/analytics"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/analytics') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-chart-line w-4 mr-3"></i>
            Analitik
        </a>
        <a href="/admin/reports"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/reports') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-file-alt w-4 mr-3"></i>
            Laporan
        </a>
    </div>
</div>

<!-- Security & System -->
<div x-data="{ expanded: <?= strpos(uri_string(), 'admin/security') === 0 || strpos(uri_string(), 'admin/system') === 0 || strpos(uri_string(), 'admin/activity-logs') === 0 || strpos(uri_string(), 'admin/backup') === 0 ? 'true' : 'false' ?> }" class="mb-2">
    <button @click="!sidebarCollapsed && (expanded = !expanded)"
        class="w-full flex items-center py-3 rounded-lg nav-item-hover <?= strpos(uri_string(), 'admin/security') === 0 || strpos(uri_string(), 'admin/system') === 0 || strpos(uri_string(), 'admin/activity-logs') === 0 || strpos(uri_string(), 'admin/backup') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
                            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
                            'px-4': !sidebarCollapsed || window.innerWidth < 1024
                        }">
        <i class="fas fa-shield-alt text-lg flex-shrink-0"></i>
        <div class="flex items-center justify-between w-full ml-3 sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            <span class="font-medium">Keamanan & Sistem</span>
            <i class="fas fa-chevron-down text-sm transition-transform duration-200"
                :class="{ 'rotate-180': expanded }"></i>
        </div>
    </button>

    <div x-show="expanded && (!sidebarCollapsed || window.innerWidth < 1024)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-1 ml-6 space-y-1"> <a href="/admin/security"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/security') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-lock w-4 mr-3"></i>
            Pengaturan Keamanan
        </a>
        <a href="/admin/activity-logs"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/activity-logs') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-history w-4 mr-3"></i>
            Log Aktivitas
        </a>
        <a href="/admin/backup"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/backup') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-download w-4 mr-3"></i>
            Backup & Restore
        </a>
        <a href="/admin/system-settings"
            class="flex items-center py-2 px-3 rounded-md text-sm nav-item-hover <?= strpos(uri_string(), 'admin/system-settings') === 0 ? 'text-white bg-white/10' : 'text-gray-300' ?>">
            <i class="fas fa-cogs w-4 mr-3"></i>
            Pengaturan Sistem
        </a>
    </div>
</div>