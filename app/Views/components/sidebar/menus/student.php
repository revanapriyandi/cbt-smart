<!-- Student Dashboard -->
<div class="tooltip">
    <a href="<?= base_url('student/dashboard') ?>"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= uri_string() === 'student/dashboard' ? 'nav-item-active' : '' ?>"
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
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Dashboard</span>
</div>

<!-- Available Exams -->
<div class="tooltip">
    <a href="<?= base_url('student/exams') ?>"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'student/exams') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
            'px-4': !sidebarCollapsed || window.innerWidth < 1024
        }">
        <i class="fas fa-clipboard-list text-lg flex-shrink-0"></i>
        <span class="ml-3 font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            Ujian Tersedia
        </span>
    </a>
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Ujian Tersedia</span>
</div>

<!-- Student Results -->
<div class="tooltip">
    <a href="<?= base_url('student/results') ?>"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'student/results') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
            'px-4': !sidebarCollapsed || window.innerWidth < 1024
        }">
        <i class="fas fa-chart-line text-lg flex-shrink-0"></i>
        <span class="ml-3 font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            Hasil Ujian
        </span>
    </a>
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Hasil Ujian</span>
</div>

<!-- My Profile -->
<div class="tooltip">
    <a href="<?= base_url('student/profile') ?>"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'student/profile') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
            'px-4': !sidebarCollapsed || window.innerWidth < 1024
        }">
        <i class="fas fa-user text-lg flex-shrink-0"></i>
        <span class="ml-3 font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            Profil Saya
        </span>
    </a>
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Profil Saya</span>
</div>