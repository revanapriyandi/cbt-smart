<!-- Role Tabs and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <!-- Role Tabs with enhanced styling -->
    <div class="border-b border-gray-200 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Filter Users</h3>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter by role</span>
            </div>
        </div>

        <nav class="-mb-px flex flex-wrap space-x-1 sm:space-x-8">
            <button onclick="filterByRole('')" id="tab-all"
                class="role-tab group border-b-2 border-indigo-500 py-3 px-1 text-sm font-semibold text-indigo-600 transition-all duration-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>All Users</span>
                    <span id="count-all" class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full">0</span>
                </div>
            </button>
            <button onclick="filterByRole('admin')" id="tab-admin"
                class="role-tab group border-b-2 border-transparent py-3 px-1 text-sm font-medium text-gray-500 hover:text-red-600 hover:border-red-300 transition-all duration-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                    </svg>
                    <span>Admins</span>
                    <span id="count-admin" class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full group-hover:bg-red-100 group-hover:text-red-800 transition-colors">0</span>
                </div>
            </button>
            <button onclick="filterByRole('teacher')" id="tab-teacher"
                class="role-tab group border-b-2 border-transparent py-3 px-1 text-sm font-medium text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-all duration-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                    </svg>
                    <span>Teachers</span>
                    <span id="count-teacher" class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full group-hover:bg-blue-100 group-hover:text-blue-800 transition-colors">0</span>
                </div>
            </button>
            <button onclick="filterByRole('student')" id="tab-student"
                class="role-tab group border-b-2 border-transparent py-3 px-1 text-sm font-medium text-gray-500 hover:text-green-600 hover:border-green-300 transition-all duration-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                    </svg>
                    <span>Students</span>
                    <span id="count-student" class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full group-hover:bg-green-100 group-hover:text-green-800 transition-colors">0</span>
                </div>
            </button>
        </nav>
    </div>

    <!-- Enhanced Search and Actions -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-end gap-4">
        <!-- Search Section -->
        <div class="flex-1 max-w-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="search-input" placeholder="Search by name, email, or username..."
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm placeholder-gray-400 transition-all duration-200">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button onclick="clearSearch()" class="text-gray-400 hover:text-gray-600 hidden" id="clear-search">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-end space-x-3">
            <div class="text-center">
                <label class="block text-xs font-medium text-gray-600 mb-1">Quick Filters</label>
                <div class="flex space-x-2">
                    <button onclick="filterByStatus('active')" id="filter-active"
                        class="status-filter px-3 py-2 text-xs font-medium rounded-lg border border-green-200 text-green-700 hover:bg-green-50 transition-colors">
                        Active Only
                    </button>
                    <button onclick="filterByStatus('inactive')" id="filter-inactive"
                        class="status-filter px-3 py-2 text-xs font-medium rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition-colors">
                        Inactive Only
                    </button>
                    <button onclick="filterByStatus('')" id="filter-all-status"
                        class="status-filter px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 bg-gray-50 transition-colors">
                        All Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>