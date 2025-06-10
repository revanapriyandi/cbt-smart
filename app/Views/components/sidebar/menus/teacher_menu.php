<!-- Teacher Dashboard -->
<div class="tooltip">
    <a href="/teacher/dashboard"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= uri_string() === 'teacher/dashboard' ? 'nav-item-active' : '' ?>"
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

<!-- Teacher Exams -->
<div class="tooltip">
    <a href="/teacher/exams"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'teacher/exams') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
            'px-4': !sidebarCollapsed || window.innerWidth < 1024
        }">
        <i class="fas fa-clipboard-list text-lg flex-shrink-0"></i>
        <span class="ml-3 font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            Ujian Saya
        </span>
    </a>
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Ujian Saya</span>
</div>

<!-- Question Banks -->
<div class="tooltip">
    <a href="/teacher/question-banks"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'teacher/question-banks') === 0 ? 'nav-item-active' : '' ?>"
        :class="{ 
            'justify-center px-2': sidebarCollapsed && window.innerWidth >= 1024,
            'px-4': !sidebarCollapsed || window.innerWidth < 1024
        }">
        <i class="fas fa-database text-lg flex-shrink-0"></i>
        <span class="ml-3 font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed || window.innerWidth < 1024">
            Bank Soal
        </span>
    </a>
    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 1024">Bank Soal</span>
</div>

<!-- Results -->
<div class="tooltip">
    <a href="/teacher/results"
        class="flex items-center py-3 mb-2 rounded-lg nav-item-hover <?= strpos(uri_string(), 'teacher/results') === 0 ? 'nav-item-active' : '' ?>"
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