<div class="relative" x-data="{ dropdownOpen: false }">
    <button @click="dropdownOpen = !dropdownOpen"
        class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200 space-x-2">
        <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center">
            <i class="fas fa-user text-white text-sm"></i>
        </div>
        <div class="hidden sm:block text-left">
            <div class="text-sm font-medium text-gray-900">
                <?= session()->get('full_name') ?? 'User' ?>
            </div>
            <div class="text-xs text-gray-500">
                <?= ucfirst(session()->get('role')) ?? 'Role' ?>
            </div>
        </div>
        <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform duration-200"
            :class="{ 'rotate-180': dropdownOpen }"></i>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">

        <!-- User Info -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="text-sm font-medium text-gray-900">
                <?= session()->get('full_name') ?? 'User Name' ?>
            </div>
            <div class="text-sm text-gray-500">
                <?= session()->get('email') ?? 'user@example.com' ?>
            </div>
        </div>

        <!-- Menu Items -->
        <a href="/profile"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            <i class="fas fa-user-circle mr-3 text-gray-400"></i>
            Profile
        </a>

        <a href="/settings"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            <i class="fas fa-cog mr-3 text-gray-400"></i>
            Pengaturan
        </a>

        <div class="border-t border-gray-200 my-1"></div>

        <a href="/logout"
            class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
            <i class="fas fa-sign-out-alt mr-3"></i>
            Logout
        </a>
    </div>
</div>