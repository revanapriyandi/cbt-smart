<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Table Header with custom styling -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Users List</h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: <span id="total-users" class="font-medium text-gray-900">0</span></span>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar (initially hidden) -->
    <div id="bulk-actions" class="hidden px-6 py-3 bg-blue-50 border-b border-blue-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-blue-900">Selected: <span id="selected-count">0</span> users</span>
                <select id="bulk-action-select" class="text-sm border-blue-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Choose action...</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="delete">Delete</option>
                </select>
                <button onclick="performBulkAction()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Apply
                </button>
            </div>
            <button onclick="clearSelection()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Clear Selection
            </button>
        </div>
    </div>

    <!-- Table Container with enhanced responsive design -->
    <div class="overflow-x-auto">
        <table id="users-table" class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-8 px-6 py-4 text-left">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-2 h-4 w-4">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>User</span>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 8a1 1 0 011.707-.707L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>Role</span>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 8a1 1 0 011.707-.707L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>Status</span>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 8a1 1 0 011.707-.707L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>Created</span>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 8a1 1 0 011.707-.707L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 015 8z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>

    <!-- Custom DataTables Pagination and Info -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Show:</label>
                <select id="dt-length" class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </div>

            <div id="dt-info" class="text-sm text-gray-700">
                <!-- DataTables info will be inserted here -->
            </div>

            <div id="dt-pagination" class="flex items-center space-x-1">
                <!-- DataTables pagination will be inserted here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="table-loading" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
        <span class="text-gray-700 font-medium">Loading users...</span>
    </div>
</div>