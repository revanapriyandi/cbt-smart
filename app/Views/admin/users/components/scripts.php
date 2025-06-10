<script>
    let currentRole = '<?= $role ?? '' ?>';
    let currentStatus = '';
    let usersTable;
    let editingUserId = null;
    $(document).ready(function() {
        // Check if DataTables is available
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables library not loaded!');
            return;
        }

        // Initialize role filter based on URL parameter
        if (currentRole) {
            initializeRoleFilter(currentRole);
        }

        // Initialize filter tab counts with data from the controller
        updateFilterCounts();

        // Initialize DataTable with enhanced styling
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('admin/users-data') ?>',
                data: function(d) {
                    d.role = currentRole;
                    d.status = currentStatus;
                    d.search = $('#search-input').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTables AJAX error:', error, code);
                    console.log('Response:', xhr.responseText);
                },
                beforeSend: function() {
                    $('#table-loading').removeClass('hidden');
                },
                complete: function() {
                    $('#table-loading').addClass('hidden');
                }
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    width: '50px',
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-2 h-4 w-4 transition-colors" value="' + data + '">';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const initial = row.full_name ? row.full_name.charAt(0).toUpperCase() : 'U';
                        const statusIndicator = row.is_active == 1 ?
                            '<div class="w-3 h-3 bg-green-400 rounded-full ring-2 ring-green-100"></div>' :
                            '<div class="w-3 h-3 bg-gray-400 rounded-full ring-2 ring-gray-100"></div>';

                        return '<div class="flex items-center group">' +
                            '<div class="relative">' +
                            '<div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">' +
                            '<span class="text-white font-semibold text-base">' + initial + '</span>' +
                            '</div>' +
                            '<div class="absolute -bottom-1 -right-1">' + statusIndicator + '</div>' +
                            '</div>' +
                            '<div class="ml-4 min-w-0 flex-1">' +
                            '<div class="text-sm font-semibold text-gray-900 truncate hover:text-indigo-600 transition-colors">' + (row.full_name || 'N/A') + '</div>' +
                            '<div class="text-sm text-gray-600 truncate">' + (row.email || 'N/A') + '</div>' +
                            '<div class="text-xs text-gray-500 font-mono">@' + (row.username || 'N/A') + '</div>' +
                            '</div>' +
                            '</div>';
                    }
                },
                {
                    data: 'role',
                    render: function(data, type, row) {
                        const roleConfig = {
                            'admin': {
                                color: 'bg-red-100 text-red-800 border border-red-200',
                                icon: '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>'
                            },
                            'teacher': {
                                color: 'bg-blue-100 text-blue-800 border border-blue-200',
                                icon: '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>'
                            },
                            'student': {
                                color: 'bg-green-100 text-green-800 border border-green-200',
                                icon: '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>'
                            }
                        };

                        const config = roleConfig[data] || {
                            color: 'bg-gray-100 text-gray-800 border border-gray-200',
                            icon: '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>'
                        };

                        return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' + config.color + '">' +
                            config.icon +
                            '<span class="ml-1">' + (data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Unknown') + '</span>' +
                            '</span>';
                    }
                },
                {
                    data: 'is_active',
                    render: function(data, type, row) {
                        const isActive = data == 1;
                        return '<div class="flex items-center">' +
                            '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' +
                            (isActive ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200') + '">' +
                            '<div class="w-2 h-2 rounded-full mr-1.5 ' + (isActive ? 'bg-green-500' : 'bg-red-500') + '"></div>' +
                            (isActive ? 'Active' : 'Inactive') +
                            '</span>' +
                            '</div>';
                    }
                },
                {
                    data: 'last_login',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<div class="text-sm text-gray-400">' +
                                '<div class="flex items-center">' +
                                '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>' +
                                '</svg>' +
                                'Never logged in' +
                                '</div>' +
                                '</div>';
                        }

                        const date = new Date(data);
                        const now = new Date();
                        const diffTime = Math.abs(now - date);
                        const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        let timeAgo = '';
                        let statusColor = '';

                        if (diffHours < 1) {
                            timeAgo = 'Just now';
                            statusColor = 'text-green-600';
                        } else if (diffHours < 24) {
                            timeAgo = diffHours + ' hours ago';
                            statusColor = 'text-green-600';
                        } else if (diffDays === 1) {
                            timeAgo = 'Yesterday';
                            statusColor = 'text-yellow-600';
                        } else if (diffDays < 7) {
                            timeAgo = diffDays + ' days ago';
                            statusColor = 'text-yellow-600';
                        } else if (diffDays < 30) {
                            timeAgo = Math.ceil(diffDays / 7) + ' weeks ago';
                            statusColor = 'text-orange-600';
                        } else {
                            timeAgo = Math.ceil(diffDays / 30) + ' months ago';
                            statusColor = 'text-red-600';
                        }

                        return '<div class="text-sm">' +
                            '<div class="font-medium text-gray-900">' + date.toLocaleDateString() + '</div>' +
                            '<div class="text-xs ' + statusColor + '">' + timeAgo + '</div>' +
                            '</div>';
                    }
                },
                {
                    data: 'activity_count',
                    render: function(data, type, row) {
                        const count = data || 0;
                        let badgeColor = '';
                        let icon = '';

                        if (count === 0) {
                            badgeColor = 'bg-gray-100 text-gray-600';
                            icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0v6m0-6V7m0 6h.01M6 20v.01"></path></svg>';
                        } else if (count < 10) {
                            badgeColor = 'bg-blue-100 text-blue-800';
                            icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>';
                        } else if (count < 50) {
                            badgeColor = 'bg-green-100 text-green-800';
                            icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>';
                        } else {
                            badgeColor = 'bg-purple-100 text-purple-800';
                            icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>';
                        }

                        return '<div class="flex items-center justify-center">' +
                            '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ' + badgeColor + '">' +
                            icon +
                            count + ' activities' +
                            '</span>' +
                            '</div>';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data, type, row) {
                        if (!data) return '<span class="text-gray-400">N/A</span>';

                        const date = new Date(data);
                        const now = new Date();
                        const diffTime = Math.abs(now - date);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        let timeAgo = '';
                        if (diffDays === 1) timeAgo = 'Yesterday';
                        else if (diffDays < 7) timeAgo = diffDays + ' days ago';
                        else if (diffDays < 30) timeAgo = Math.ceil(diffDays / 7) + ' weeks ago';
                        else timeAgo = Math.ceil(diffDays / 30) + ' months ago';

                        return '<div class="text-sm">' +
                            '<div class="font-medium text-gray-900">' + date.toLocaleDateString() + '</div>' +
                            '<div class="text-xs text-gray-500">' + timeAgo + '</div>' +
                            '</div>';
                    }
                }, {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    width: '150px',
                    render: function(data, type, row) {
                        return '<div class="flex items-center justify-center space-x-1">' +
                            '<button onclick="viewUserDetails(' + data + ')" class="inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors group" title="View Details">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>' +
                            '</svg>' +
                            '</button>' +
                            '<button onclick="editUser(' + data + ')" class="inline-flex items-center p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors group" title="Edit User">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>' +
                            '</svg>' +
                            '</button>' +
                            '<button onclick="deleteUser(' + data + ')" class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors group" title="Delete User">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>' +
                            '</svg>' +
                            '</button>' +
                            '</div>';
                    }
                }
            ],
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            responsive: true,
            dom: 'rt', // Remove all default controls, we'll use custom ones
            language: {
                processing: '<div class="flex items-center justify-center p-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mr-3"></div>Loading users...</div>',
                emptyTable: '<div class="text-center py-8"><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p></div>',
                info: "Showing _START_ to _END_ of _TOTAL_ users",
                infoEmpty: "No users available",
                infoFiltered: "(filtered from _MAX_ total users)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function(settings) {
                // Update custom pagination and info
                updateCustomControls();
                // Update selection state
                updateSelectionState();
            },
            rowCallback: function(row, data) {
                // Add hover effects
                $(row).addClass('hover:bg-gray-50 transition-colors cursor-pointer');

                // Add row click handler (excluding action buttons)
                $(row).on('click', function(e) {
                    if (!$(e.target).closest('button, input').length) {
                        // Toggle row selection
                        const checkbox = $(row).find('.user-checkbox');
                        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                    }
                });
            }
        }); // Search functionality with clear button
        $('#search-input').on('keyup', function() {
            const searchValue = $(this).val();
            if (searchValue.length > 0) {
                $('#clear-search').removeClass('hidden');
            } else {
                $('#clear-search').addClass('hidden');
            }
            usersTable.ajax.reload();
        });

        // Custom length change handler
        $('#dt-length').on('change', function() {
            usersTable.page.len($(this).val()).draw();
        });

        // Select all checkbox
        $('#select-all').on('change', function() {
            $('.user-checkbox').prop('checked', $(this).prop('checked'));
            updateSelectionState();
        });

        // Individual checkbox change
        $(document).on('change', '.user-checkbox', function() {
            updateSelectionState();
        });

        // User form submission
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            saveUser();
        });

        // Import form submission
        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            importUsers();
        });
    });

    function filterByRole(role) {
        currentRole = role;

        // Update tab styles with enhanced animation
        $('.role-tab').removeClass('border-indigo-500 text-indigo-600 border-red-500 text-red-600 border-blue-500 text-blue-600 border-green-500 text-green-600')
            .addClass('border-transparent text-gray-500');

        const activeTab = $('#tab-' + (role || 'all'));
        activeTab.removeClass('border-transparent text-gray-500');

        // Apply role-specific colors
        if (role === 'admin') {
            activeTab.addClass('border-red-500 text-red-600');
        } else if (role === 'teacher') {
            activeTab.addClass('border-blue-500 text-blue-600');
        } else if (role === 'student') {
            activeTab.addClass('border-green-500 text-green-600');
        } else {
            activeTab.addClass('border-indigo-500 text-indigo-600');
        } // Reload table
        // usersTable.ajax.reload();
    }

    function initializeRoleFilter(role) {
        // Set the current role and update tab styles
        filterByRole(role);
    }

    function filterByStatus(status) {
        currentStatus = status;

        // Update status filter buttons
        $('.status-filter').removeClass('bg-gray-50 bg-green-50 bg-red-50')
            .addClass('hover:bg-gray-50');

        if (status === 'active') {
            $('#filter-active').addClass('bg-green-50').removeClass('hover:bg-gray-50');
        } else if (status === 'inactive') {
            $('#filter-inactive').addClass('bg-red-50').removeClass('hover:bg-gray-50');
        } else {
            $('#filter-all-status').addClass('bg-gray-50').removeClass('hover:bg-gray-50');
        }

        // Reload table
        usersTable.ajax.reload();
    }

    function clearSearch() {
        $('#search-input').val('');
        $('#clear-search').addClass('hidden');
        usersTable.ajax.reload();
    }

    function toggleBulkActions() {
        const checkedBoxes = $('.user-checkbox:checked').length;
        if (checkedBoxes > 0) {
            $('#bulk-actions').removeClass('hidden');
            $('#selected-count').text(checkedBoxes);
        } else {
            $('#bulk-actions').addClass('hidden');
        }
    }

    // New enhanced functions for better UX
    function updateSelectionState() {
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedBoxes = $('.user-checkbox:checked').length;

        // Update select all checkbox state
        if (checkedBoxes === 0) {
            $('#select-all').prop('indeterminate', false).prop('checked', false);
        } else if (checkedBoxes === totalCheckboxes) {
            $('#select-all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all').prop('indeterminate', true).prop('checked', false);
        }

        // Update bulk actions
        toggleBulkActions();
    }

    function updateCustomControls() {
        const info = usersTable.page.info();

        // Update total users count with fallback
        const totalUsers = info.recordsTotal || 0;
        $('#total-users').text(totalUsers);

        // Update info text
        if (totalUsers === 0) {
            $('#dt-info').html('<span class="text-gray-500">No users to display</span>');
        } else {
            const start = info.start + 1;
            const end = info.end;
            const filtered = info.recordsFiltered || totalUsers;

            let infoText = `Showing <span class="font-medium">${start}</span> to <span class="font-medium">${end}</span> of <span class="font-medium">${totalUsers}</span> users`;

            if (filtered !== totalUsers) {
                infoText += ` (filtered from ${totalUsers} total)`;
            }

            $('#dt-info').html(infoText);
        }

        // Update custom pagination
        updatePagination(info);
    }

    function updatePagination(info) {
        const currentPage = info.page + 1;
        const totalPages = info.pages;
        const paginationContainer = $('#dt-pagination');

        if (totalPages <= 1) {
            paginationContainer.html('');
            return;
        }

        let pagination = '';

        // Previous button
        pagination += `<button onclick="usersTable.page('previous').draw('page')" ${currentPage === 1 ? 'disabled' : ''} 
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>`;

        // Page numbers
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            pagination += `<button onclick="usersTable.page(0).draw('page')" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">1</button>`;
            if (startPage > 2) {
                pagination += `<span class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 bg-white text-gray-500">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            pagination += `<button onclick="usersTable.page(${i - 1}).draw('page')" 
                class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 transition-colors ${
                    isActive 
                        ? 'bg-indigo-600 text-white border-indigo-600 z-10' 
                        : 'bg-white text-gray-700 hover:bg-gray-50'
                }">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination += `<span class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 bg-white text-gray-500">...</span>`;
            }
            pagination += `<button onclick="usersTable.page(${totalPages - 1}).draw('page')" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">${totalPages}</button>`;
        }

        // Next button
        pagination += `<button onclick="usersTable.page('next').draw('page')" ${currentPage === totalPages ? 'disabled' : ''} 
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>`;

        paginationContainer.html(pagination);
    }

    function clearSelection() {
        $('.user-checkbox').prop('checked', false);
        $('#select-all').prop('checked', false).prop('indeterminate', false);
        toggleBulkActions();
    }

    function openCreateModal() {
        editingUserId = null;
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userPassword').required = true;
        document.getElementById('userActive').checked = true;

        // Hide activity sidebar for new users
        const activitySidebar = document.getElementById('activitySidebar');
        const modalGrid = document.getElementById('modalGrid');
        const formSection = document.getElementById('formSection');

        activitySidebar.style.display = 'none';
        modalGrid.className = 'grid grid-cols-1 h-full';
        formSection.className = 'p-6';

        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(userId) {
        editingUserId = userId;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userPassword').required = false;

        // Show loading state
        showActivityLoading();

        // Fetch user data
        fetch('<?= base_url('admin/users/get') ?>/' + userId)
            .then(response => response.json())
            .then(user => {
                document.getElementById('userName').value = user.username || '';
                document.getElementById('userFullName').value = user.full_name || '';
                document.getElementById('userEmail').value = user.email || '';
                document.getElementById('userRole').value = user.role || '';
                document.getElementById('userActive').checked = user.is_active == 1;

                // Update user info in modal header
                document.getElementById('userNameDisplay').textContent = user.full_name || user.username;
                document.getElementById('userRoleDisplay').textContent = capitalizeRole(user.role);
                document.getElementById('userInitial').textContent = (user.full_name || user.username).charAt(0).toUpperCase(); // Show/hide activity sidebar based on user role
                const activitySidebar = document.getElementById('activitySidebar');
                const modalGrid = document.getElementById('modalGrid');
                const formSection = document.getElementById('formSection');

                if (user.role === 'student') {
                    activitySidebar.style.display = 'block';
                    modalGrid.className = 'grid grid-cols-1 lg:grid-cols-3 h-full';
                    formSection.className = 'lg:col-span-2 p-6 border-r border-gray-200';
                    // Load user activity data only for students
                    loadUserActivityData(userId);
                } else {
                    activitySidebar.style.display = 'none';
                    modalGrid.className = 'grid grid-cols-1 h-full';
                    formSection.className = 'p-6';
                    hideActivityLoading();
                }
            })
            .catch(error => {
                console.error('Error fetching user:', error);
                alert('Error loading user data');
                hideActivityLoading();
            });

        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function saveUser() {
        const formData = new FormData(document.getElementById('userForm'));
        const url = editingUserId ?
            '<?= base_url('admin/users/update') ?>/' + editingUserId :
            '<?= base_url('admin/users/store') ?>';

        fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    usersTable.ajax.reload();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error saving user', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving user', 'error');
            });
    }

    function deleteUser(userId) {
        // First check if user can be safely deleted
        fetch('<?= base_url('admin/users/check-deletion') ?>/' + userId)
            .then(response => response.json())
            .then(checkData => {
                if (!checkData.success) {
                    showNotification(checkData.message || 'Error checking user deletion status', 'error');
                    return;
                }

                let confirmMessage = 'Are you sure you want to delete this user?';
                if (!checkData.canDelete) {
                    confirmMessage = `Warning: This user has related records (${checkData.relatedRecords.join(', ')}). ` +
                        'Deleting this user will also delete all related records. ' +
                        'Are you sure you want to continue?';
                }

                if (confirm(confirmMessage)) {
                    // Show loading indicator
                    const loadingNotification = showNotification('Deleting user...', 'info');

                    fetch('<?= base_url('admin/users/delete') ?>/' + userId, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading notification
                            if (loadingNotification && loadingNotification.parentElement) {
                                loadingNotification.remove();
                            }

                            if (data.success) {
                                usersTable.ajax.reload();
                                showNotification(data.message, 'success');
                            } else {
                                showNotification(data.message || 'Error deleting user', 'error');
                            }
                        })
                        .catch(error => {
                            // Remove loading notification
                            if (loadingNotification && loadingNotification.parentElement) {
                                loadingNotification.remove();
                            }
                            console.error('Error:', error);
                            showNotification('Error deleting user', 'error');
                        });
                }
            })
            .catch(error => {
                console.error('Error checking deletion status:', error);
                // Fallback to simple confirmation
                if (confirm('Are you sure you want to delete this user?')) {
                    fetch('<?= base_url('admin/users/delete') ?>/' + userId, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                usersTable.ajax.reload();
                                showNotification(data.message, 'success');
                            } else {
                                showNotification(data.message || 'Error deleting user', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Error deleting user', 'error');
                        });
                }
            });
    }

    function performBulkAction() {
        const action = document.getElementById('bulk-action-select').value;
        const userIds = $('.user-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action) {
            alert('Please select an action');
            return;
        }
        if (userIds.length === 0) {
            alert('Please select users');
            return;
        }

        if (action === 'delete') {
            const confirmMessage = `Are you sure you want to delete ${userIds.length} selected user(s)?\n\n` +
                'Warning: This will also delete all related records (exams, answers, activity logs, etc.) ' +
                'for these users. This action cannot be undone.';
            if (!confirm(confirmMessage)) {
                return;
            }
        }

        // Show loading indicator for bulk actions
        const loadingMessage = `Processing ${action} for ${userIds.length} user(s)...`;
        const loadingNotification = showNotification(loadingMessage, 'info');

        const formData = new FormData();
        formData.append('action', action);
        formData.append('user_ids', JSON.stringify(userIds));
        fetch('<?= base_url('admin/users/bulk-action') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading notification
                if (loadingNotification && loadingNotification.parentElement) {
                    loadingNotification.remove();
                }

                if (data.success || (data.details && data.details.success > 0)) {
                    usersTable.ajax.reload();
                    showNotification(data.message, data.success ? 'success' : 'warning');
                    $('#select-all').prop('checked', false);
                    toggleBulkActions();
                } else {
                    showNotification(data.message || 'Error performing bulk action', 'error');
                }
            })
            .catch(error => {
                // Remove loading notification
                if (loadingNotification && loadingNotification.parentElement) {
                    loadingNotification.remove();
                }
                console.error('Error:', error);
                showNotification('Error performing bulk action', 'error');
            });
    }

    function exportUsers() {
        const role = currentRole;
        const status = currentStatus;
        let url = '<?= base_url('admin/users/export') ?>';

        const params = [];
        if (role) params.push('role=' + role);
        if (status) params.push('status=' + status);

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        window.location.href = url;
    }

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function importUsers() {
        const formData = new FormData(document.getElementById('importForm'));

        fetch('<?= base_url('admin/users/import') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeImportModal();
                    usersTable.ajax.reload();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error importing users', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error importing users', 'error');
            });
    }

    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 shadow-lg rounded-lg p-4 ${
            type === 'success' ? 'border-green-400' : 
            type === 'warning' ? 'border-yellow-400' : 
            type === 'info' ? 'border-blue-400' : 'border-red-400'
        }`;

        const iconColor = type === 'success' ? 'text-green-400' :
            type === 'warning' ? 'text-yellow-400' :
            type === 'info' ? 'text-blue-400' : 'text-red-400';

        const iconPath = type === 'success' ?
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
            type === 'warning' ?
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>' :
            type === 'info' ?
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

        notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${iconPath}
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds (except for info notifications which are for loading)
        if (type !== 'info') {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Return the notification element so it can be removed manually
        return notification;
    } // Close modals when clicking outside
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImportModal();
        }
    });

    // User Activity Data Functions
    function loadUserActivityData(userId) {
        // Load activity statistics
        fetch('<?= base_url('admin/users/activity-stats') ?>/' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateActivityStats(data.data);
                } else {
                    console.error('Error loading activity stats:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching activity stats:', error);
            });

        // Load recent activities
        fetch('<?= base_url('admin/users/recent-activities') ?>/' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateRecentActivities(data.data);
                } else {
                    console.error('Error loading recent activities:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching recent activities:', error);
            });

        // Load exam performance
        fetch('<?= base_url('admin/users/exam-performance') ?>/' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateExamPerformance(data.data);
                } else {
                    console.error('Error loading exam performance:', data.message);
                }
                hideActivityLoading();
            })
            .catch(error => {
                console.error('Error fetching exam performance:', error);
                hideActivityLoading();
            });
    }

    function updateActivityStats(stats) {
        document.getElementById('totalExams').textContent = stats.total_exams;
        document.getElementById('averageScore').textContent = stats.average_score + '%';
        document.getElementById('loginCount').textContent = stats.login_count;
    }

    function updateRecentActivities(activities) {
        const container = document.getElementById('recentActivity');
        if (!activities || activities.length === 0) {
            container.innerHTML = '<div class="text-center py-8 text-gray-500">No recent activities</div>';
            return;
        }

        const activitiesHtml = activities.map(activity => `
            <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0">
                <div class="flex-shrink-0">
                    <i class="${activity.icon} w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">${activity.description}</p>
                    <p class="text-xs text-gray-500">${activity.time_ago}</p>
                </div>
            </div>
        `).join('');

        container.innerHTML = activitiesHtml;
    }

    function updateExamPerformance(examResults) {
        // This function can be extended to show exam performance data
        // For now, we'll just log it
        console.log('Exam performance data:', examResults);
    }

    function showActivityLoading() {
        const loadingHtml = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <span class="ml-2 text-gray-600">Loading activity data...</span>
            </div>
        `;
        document.getElementById('totalExams').textContent = '...';
        document.getElementById('averageScore').textContent = '...';
        document.getElementById('loginCount').textContent = '...';
        document.getElementById('recentActivity').innerHTML = loadingHtml;
    }

    function hideActivityLoading() {
        // Loading state will be replaced by actual data
    }

    function capitalizeRole(role) {
        return role.charAt(0).toUpperCase() + role.slice(1);
    }

    // Function to view user details
    function viewUserDetails(userId) {
        window.location.href = '<?= base_url('admin/users/view/') ?>' + userId;
    }

    // Function to update filter tab counts
    function updateFilterCounts() {
        // Get counts from PHP variables passed to the view
        const counts = {
            total: <?= $totalUsers ?? 0 ?>,
            admin: <?= $totalAdmins ?? 0 ?>,
            teacher: <?= $totalTeachers ?? 0 ?>,
            student: <?= $totalStudents ?? 0 ?>
        };

        // Debug logging to check if values are being passed correctly
        console.log('Updating filter counts:', counts);

        // Update the count spans in the filter tabs
        const countAllElement = document.getElementById('count-all');
        const countAdminElement = document.getElementById('count-admin');
        const countTeacherElement = document.getElementById('count-teacher');
        const countStudentElement = document.getElementById('count-student');
        const totalUsersElement = document.getElementById('total-users-table');

        if (countAllElement) countAllElement.textContent = counts.total;
        if (countAdminElement) countAdminElement.textContent = counts.admin;
        if (countTeacherElement) countTeacherElement.textContent = counts.teacher;
        if (countStudentElement) countStudentElement.textContent = counts.student;

        // Update the table header total count
        if (totalUsersElement) totalUsersElement.textContent = counts.total;

        console.log('Filter counts updated successfully');
    }
</script>