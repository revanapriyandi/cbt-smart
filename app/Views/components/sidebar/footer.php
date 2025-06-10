<div class="p-4 border-t border-white/10">
    <button @click="sidebarCollapsed = !sidebarCollapsed"
        class="hidden lg:flex items-center justify-center w-full h-10 rounded-lg bg-white/10 hover:bg-white/20 sidebar-transition"
        :class="{ 'px-2': sidebarCollapsed, 'px-4': !sidebarCollapsed }">
        <i class="fas fa-angles-left text-lg transition-transform duration-200"
            :class="{ 'rotate-180': sidebarCollapsed }"></i>
        <span class="ml-2 text-sm font-medium sidebar-transition overflow-hidden"
            x-show="!sidebarCollapsed">
            Collapse
        </span>
    </button>
</div>