<script>
    // Enhanced Classes Management System
    let classesTable;

    $(document).ready(function() {
        console.log('Initializing Enhanced Classes Management System');

        // Initialize DataTable with enhanced configuration
        classesTable = $('#classesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>Loading classes...</div>',
                emptyTable: '<div class="text-center py-8"><div class="text-gray-400 mb-2"><svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><p class="text-gray-600 text-lg font-medium">No classes found</p><p class="text-gray-500 text-sm">Start by creating your first class</p></div>',
                zeroRecords: '<div class="text-center py-8"><div class="text-gray-400 mb-2"><svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div><p class="text-gray-600 text-lg font-medium">No matching classes found</p><p class="text-gray-500 text-sm">Try adjusting your search terms</p></div>',
                info: 'Showing _START_ to _END_ of _TOTAL_ classes',
                infoEmpty: 'Showing 0 to 0 of 0 classes',
                infoFiltered: '(filtered from _MAX_ total classes)',
                lengthMenu: 'Show _MENU_ classes per page',
                search: '',
                searchPlaceholder: 'Search classes...'
            },
            ajax: {
                url: '<?= base_url('admin/classes/datatables') ?>',
                type: 'POST',
                data: function(d) {
                    d.level_filter = $('#levelFilter').val();
                    d.status_filter = $('#statusFilter').val();
                    d.academic_year_filter = $('#academicYearFilter').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTables AJAX Error:', error, code, xhr.responseText);
                    showAlert('error', 'Failed to load classes data. Please refresh the page.');
                },
                complete: function(response) {
                    updateRecordCount();
                    updateBulkActionsVisibility();
                }
            },
            columns: [{
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '50px',
                    className: 'text-center px-6 py-4',
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="class-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2 h-4 w-4" value="' + row.id + '">';
                    }
                },
                {
                    data: 'name',
                    className: 'px-6 py-4',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let html = '<div class="flex items-center space-x-3">';
                            html += '<div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">';
                            html += '<span class="text-white font-semibold text-sm">' + (data ? data.charAt(0).toUpperCase() : 'C') + '</span>';
                            html += '</div>';
                            html += '<div>';
                            html += '<div class="font-semibold text-gray-900">' + (data || 'Unnamed Class') + '</div>';
                            if (row.description) {
                                html += '<div class="text-sm text-gray-500 max-w-xs truncate">' + row.description + '</div>';
                            }
                            html += '</div>';
                            html += '</div>';
                            return html;
                        }
                        return data || '';
                    }
                },
                {
                    data: 'level',
                    className: 'px-6 py-4 text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data) {
                                let badgeColor = data == 10 ? 'bg-green-100 text-green-800' :
                                    data == 11 ? 'bg-blue-100 text-blue-800' :
                                    'bg-purple-100 text-purple-800';
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + badgeColor + '">Grade ' + data + '</span>';
                            }
                            return '<span class="text-gray-400">-</span>';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'teacher_name',
                    className: 'px-6 py-4 hidden md:table-cell',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data) {
                                return '<div class="flex items-center space-x-2">' +
                                    '<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">' +
                                    '<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>' +
                                    '</svg></div>' +
                                    '<span class="text-sm font-medium text-gray-900">' + data + '</span>' +
                                    '</div>';
                            }
                            return '<span class="text-gray-400 italic text-sm">No Teacher Assigned</span>';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'capacity',
                    className: 'px-6 py-4 text-center hidden lg:table-cell',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let capacity = parseInt(data) || 0;
                            let studentCount = parseInt(row.student_count) || 0;
                            let percentage = capacity > 0 ? Math.round((studentCount / capacity) * 100) : 0;

                            let colorClass = percentage >= 90 ? 'text-red-600 bg-red-50' :
                                percentage >= 70 ? 'text-yellow-600 bg-yellow-50' :
                                'text-green-600 bg-green-50';

                            return '<div class="text-center">' +
                                '<div class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm font-medium ' + colorClass + '">' +
                                '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>' +
                                '</svg>' + capacity +
                                '</div></div>';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'student_count',
                    className: 'px-6 py-4 text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let studentCount = parseInt(data) || 0;
                            let capacity = parseInt(row.capacity) || 0;
                            let percentage = capacity > 0 ? Math.round((studentCount / capacity) * 100) : 0;

                            let progressColor = percentage >= 90 ? 'bg-red-500' :
                                percentage >= 70 ? 'bg-yellow-500' :
                                'bg-green-500';

                            let html = '<div class="text-center">';
                            html += '<div class="font-semibold text-gray-900 mb-1">' + studentCount + '</div>';
                            if (capacity > 0) {
                                html += '<div class="w-full bg-gray-200 rounded-full h-1.5 mb-1">';
                                html += '<div class="' + progressColor + ' h-1.5 rounded-full" style="width: ' + Math.min(percentage, 100) + '%"></div>';
                                html += '</div>';
                                html += '<div class="text-xs text-gray-500">' + percentage + '%</div>';
                            }
                            html += '</div>';
                            return html;
                        }
                        return data || '';
                    }
                },
                {
                    data: 'academic_year',
                    className: 'px-6 py-4 text-center hidden xl:table-cell',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data) {
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' +
                                    '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>' +
                                    '</svg>' + data +
                                    '</span>';
                            }
                            return '<span class="text-gray-400">-</span>';
                        }
                        return data || '';
                    }
                },
                {
                    data: 'status',
                    className: 'px-6 py-4 text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let isActive = data == 1 || data == '1' || data === 'active';
                            if (isActive) {
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' +
                                    '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">' +
                                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>' +
                                    '</svg>Active</span>';
                            } else {
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' +
                                    '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">' +
                                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>' +
                                    '</svg>Inactive</span>';
                            }
                        }
                        return data || '';
                    }
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'px-6 py-4 text-center',
                    width: '120px',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let html = '<div class="flex items-center justify-center space-x-1">';

                            // View button
                            html += '<a href="<?= base_url('admin/classes') ?>/' + row.id + '" class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-all duration-200" title="View Class">';
                            html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                            html += '</svg></a>';

                            // Edit button
                            html += '<button onclick="editClass(' + row.id + ')" class="inline-flex items-center p-1.5 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-lg transition-all duration-200" title="Edit Class">';
                            html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>';
                            html += '</svg></button>';

                            // Delete button
                            html += '<button onclick="deleteClass(' + row.id + ', \'' + (row.name || 'Unnamed Class').replace(/'/g, '\\\'') + '\')" class="inline-flex items-center p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-all duration-200" title="Delete Class">';
                            html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>';
                            html += '</svg></button>';

                            html += '</div>';
                            return html;
                        }
                        return '';
                    }
                }
            ],
            order: [
                [2, 'asc'], // Sort by level first
                [1, 'asc'] // Then by name
            ],
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"flex items-center space-x-2"l><"flex-1 flex justify-center sm:justify-end"f>>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4"<"flex items-center"i><"flex items-center space-x-2"p>>',
            drawCallback: function(settings) {
                updateRecordCount();
                updateBulkActionsVisibility();

                // Add custom styling to pagination
                $('.paginate_button').addClass('px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700 transition-colors');
                $('.paginate_button.current').addClass('bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:text-white');
                $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');
            }
        });

        // Enhanced event handlers and utility functions

        // Checkbox selection handlers
        $('#select-all').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('.class-checkbox').prop('checked', isChecked);
            updateBulkActionsVisibility();
        });

        $(document).on('change', '.class-checkbox', function() {
            updateBulkActionsVisibility();

            // Update select all checkbox
            let totalCheckboxes = $('.class-checkbox').length;
            let checkedCheckboxes = $('.class-checkbox:checked').length;
            $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            $('#select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
        });

        // Load statistics on page load
        loadStatistics();

        // Auto-refresh statistics every 30 seconds
        setInterval(loadStatistics, 30000);
    });

    // Enhanced utility functions
    function updateRecordCount() {
        try {
            let info = classesTable.page.info();
            let totalRecords = info.recordsDisplay || 0;
            $('#recordCount').text(totalRecords + ' ' + (totalRecords === 1 ? 'class' : 'classes'));
        } catch (error) {
            console.warn('Error updating record count:', error);
        }
    }

    function updateBulkActionsVisibility() {
        let selectedCount = $('.class-checkbox:checked').length;
        $('#selectedCount').text(selectedCount + ' selected');

        if (selectedCount > 0) {
            $('#bulkActions').removeClass('hidden').addClass('flex');
        } else {
            $('#bulkActions').addClass('hidden').removeClass('flex');
        }
    }

    function clearSelection() {
        $('.class-checkbox, #select-all').prop('checked', false);
        updateBulkActionsVisibility();
    }

    // Enhanced refresh function
    function refreshTable() {
        showLoading(true);
        classesTable.ajax.reload(function() {
            showLoading(false);
            loadStatistics();
            showAlert('success', 'Classes data refreshed successfully');
        }, false);
    }

    // Loading indicator
    function showLoading(show) {
        if (show) {
            $('#table-loading').removeClass('hidden');
        } else {
            $('#table-loading').addClass('hidden');
        }
    }

    // Enhanced statistics loading
    function loadStatistics() {
        $.ajax({
            url: '<?= base_url('admin/classes/statistics') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#totalClasses').text(data.data.totalClasses || 0);
                    $('#activeClasses').text(data.data.activeClasses || 0);
                    $('#totalStudents').text(data.data.totalStudents || 0);
                    $('#totalTeachers').text(data.data.totalTeachers || 0);

                    // Update progress indicators if needed
                    updateStatisticsProgress(data.data);
                }
            },
            error: function(xhr, status, error) {
                console.warn('Failed to load statistics:', error);
            }
        });
    }

    function updateStatisticsProgress(data) {
        // Add visual progress indicators for statistics cards
        const cards = [{
                id: 'totalClasses',
                value: data.totalClasses || 0,
                max: 100
            },
            {
                id: 'activeClasses',
                value: data.activeClasses || 0,
                max: data.totalClasses || 1
            },
            {
                id: 'totalStudents',
                value: data.totalStudents || 0,
                max: 1000
            },
            {
                id: 'totalTeachers',
                value: data.totalTeachers || 0,
                max: 50
            }
        ];

        cards.forEach(card => {
            let percentage = Math.min((card.value / card.max) * 100, 100);
            let $cardElement = $('#' + card.id).closest('.bg-white');

            // Add subtle background progress if doesn't exist
            if (!$cardElement.find('.progress-bg').length) {
                $cardElement.append('<div class="progress-bg absolute bottom-0 left-0 h-1 bg-gradient-to-r from-blue-400 to-purple-500 transition-all duration-1000" style="width: ' + percentage + '%"></div>');
            } else {
                $cardElement.find('.progress-bg').css('width', percentage + '%');
            }
        });
    }

    function toggleView(view) {
        if (view === 'table') {
            $('#tableView').removeClass('hidden');
            $('#gridView').addClass('hidden');
            $('#tableViewBtn').addClass('bg-indigo-50 text-indigo-600').removeClass('text-gray-600');
            $('#gridViewBtn').removeClass('bg-indigo-50 text-indigo-600').addClass('text-gray-600');
        } else {
            $('#tableView').addClass('hidden');
            $('#gridView').removeClass('hidden');
            $('#gridViewBtn').addClass('bg-indigo-50 text-indigo-600').removeClass('text-gray-600');
            $('#tableViewBtn').removeClass('bg-indigo-50 text-indigo-600').addClass('text-gray-600');
            loadGridView();
        }
    }

    function loadGridView() {
        // Implementation for grid view
        $('#gridView').html('<div class="col-span-full text-center py-12 text-gray-500">Grid view will be implemented</div>');
    }

    function resetFilters() {
        $('#searchInput').val('');
        $('#levelFilter, #statusFilter, #academicYearFilter').val('');
        classesTable.search('').ajax.reload();
    }

    function performBulkAction() {
        let action = $('#bulk-action-select').val();
        if (!action) {
            showAlert('warning', 'Please select an action');
            return;
        }
        bulkAction(action);
    }

    function showBulkActions() {
        $('#bulkActionsModal').removeClass('hidden').addClass('flex');
    }

    function closeBulkModal() {
        $('#bulkActionsModal').removeClass('flex').addClass('hidden');
    }

    function bulkAction(action) {
        let selectedIds = [];
        $('.class-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            showAlert('warning', 'Please select at least one class');
            return;
        }

        // Confirm action
        let actionText = {
            'activate': 'activate',
            'deactivate': 'deactivate',
            'delete': 'delete',
            'export': 'export'
        } [action] || action;

        if (action === 'delete') {
            if (!confirm(`Are you sure you want to ${actionText} ${selectedIds.length} selected classes? This action cannot be undone.`)) {
                return;
            }
        } else if (action !== 'export') {
            if (!confirm(`Are you sure you want to ${actionText} ${selectedIds.length} selected classes?`)) {
                return;
            }
        }

        if (action === 'export') {
            // Handle export
            let url = '<?= base_url('admin/classes/export') ?>';
            url += '?ids=' + selectedIds.join(',');
            window.open(url, '_blank');
            closeBulkModal();
            return;
        }

        // Perform bulk action via AJAX
        $.ajax({
            url: '<?= base_url('admin/classes/bulk-action') ?>',
            type: 'POST',
            data: {
                action: action,
                ids: selectedIds,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    classesTable.ajax.reload();
                    loadStatistics();
                    clearSelection();
                } else {
                    showAlert('error', response.message);
                }
                closeBulkModal();
            },
            error: function() {
                showAlert('error', 'Failed to perform bulk action');
                closeBulkModal();
            }
        });
    }

    function openCreateClassModal() {
        // Reset modal state
        $('#modalTitle').text('Add New Class');
        $('#classNameDisplay').text('Create a new class');
        $('#classLevelDisplay').hide();
        $('#classInitial').text('C');

        // Reset form
        $('#classForm')[0].reset();
        $('#classId').val('');
        $('#classIsActive').prop('checked', true);

        // Hide additional info section
        $('#additionalInfo').addClass('hidden');

        // Update modal grid layout for create mode
        $('#modalGrid').removeClass('lg:grid-cols-3').addClass('grid-cols-1');
        $('#statisticsSidebar').addClass('hidden');

        // Load teachers dropdown
        loadTeachersDropdown();

        // Show modal
        $('#classModal').removeClass('hidden').addClass('flex');
    }

    function openCreateModal() {
        openCreateClassModal();
    }

    function editClass(id) {
        openEditModal(id);
    }

    function openEditModal(id) {
        // Load class data
        $.get(`<?= base_url('admin/classes/show') ?>/${id}`, function(response) {
            if (response.success && response.data) {
                let data = response.data;

                // Update modal header
                $('#modalTitle').text('Edit Class');
                $('#classNameDisplay').text(data.name || 'Class Details');
                $('#classInitial').text((data.name || 'C').charAt(0).toUpperCase());
                if (data.level) {
                    $('#classLevelDisplay').text('Grade ' + data.level).show();
                }

                // Populate form fields
                $('#classId').val(data.id);
                $('#className').val(data.name);
                $('#classLevel').val(data.level);
                $('#classCapacity').val(data.capacity);
                $('#classTeacher').val(data.teacher_id);
                $('#classAcademicYear').val(data.academic_year);
                $('#classDescription').val(data.description);
                $('#classIsActive').prop('checked', data.is_active == 1);

                // Show additional info section
                $('#additionalInfo').removeClass('hidden');
                $('#classCreatedAt').text(data.created_at ? new Date(data.created_at).toLocaleDateString() : '-');
                $('#classUpdatedAt').text(data.updated_at ? new Date(data.updated_at).toLocaleDateString() : '-');
                $('#classStudentCount').text(data.student_count || '0');

                // Update modal layout for edit mode with sidebar
                $('#modalGrid').removeClass('grid-cols-1').addClass('lg:grid-cols-3');
                $('#statisticsSidebar').removeClass('hidden');

                // Load class statistics
                loadClassStatistics(id);

                // Show modal
                $('#classModal').removeClass('hidden').addClass('flex');
            } else {
                showAlert('error', 'Failed to load class data');
            }
        }).fail(function() {
            showAlert('error', 'Error loading class data');
        });
    }

    function loadClassStatistics(classId) {
        // Update statistics cards
        $.get(`<?= base_url('admin/classes/statistics') ?>/${classId}`, function(response) {
            if (response.success && response.data) {
                let stats = response.data;
                $('#totalStudents').text(stats.students || '0');
                $('#capacityUsage').text(stats.capacity_usage || '0%');
                $('#activeExams').text(stats.active_exams || '0');
            }
        });

        // Load student list
        $.get(`<?= base_url('admin/classes/students') ?>/${classId}`, function(response) {
            if (response.success && response.data) {
                let studentList = $('#studentList');
                studentList.empty();

                if (response.data.length > 0) {
                    response.data.forEach(function(student) {
                        studentList.append(`
                        <div class="p-3 flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-medium text-sm">${student.full_name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">${student.full_name}</p>
                                <p class="text-xs text-gray-500">${student.username}</p>
                            </div>
                            <div class="text-xs text-gray-400">
                                ${student.last_login ? new Date(student.last_login).toLocaleDateString() : 'Never'}
                            </div>
                        </div>
                    `);
                    });
                } else {
                    studentList.append(`
                    <div class="p-4 text-center text-gray-500 text-sm">
                        No students enrolled
                    </div>
                `);
                }
            }
        });
    }

    function deleteClass(id, name) {
        if (!confirm(`Are you sure you want to delete the class "${name}"? This action cannot be undone.`)) {
            return;
        }

        $.ajax({
            url: `<?= base_url('admin/classes/delete') ?>/${id}`,
            type: 'DELETE',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    classesTable.ajax.reload();
                    loadStatistics();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to delete class');
            }
        });
    }

    function closeClassModal() {
        $('#classModal').addClass('hidden');
        resetClassForm();
    }

    function resetClassForm() {
        $('#classForm')[0].reset();
        $('#classId').val('');
        $('#classIsActive').prop('checked', true);
        clearValidationErrors();
    }

    // Enhanced Validation
    function validateClassForm() {
        clearValidationErrors();
        let isValid = true;
        let errors = {};

        // Required field validation
        let className = $('#className').val().trim();
        if (!className) {
            errors.name = 'Class name is required';
            isValid = false;
        }

        let classLevel = $('#classLevel').val();
        if (!classLevel) {
            errors.level = 'Grade level is required';
            isValid = false;
        }

        let classCapacity = $('#classCapacity').val();
        if (!classCapacity || classCapacity < 1 || classCapacity > 50) {
            errors.capacity = 'Capacity must be between 1 and 50 students';
            isValid = false;
        }

        let academicYear = $('#classAcademicYear').val();
        if (!academicYear) {
            errors.academic_year = 'Academic year is required';
            isValid = false;
        }

        if (!isValid) {
            showValidationErrors(errors);
        }

        return isValid;
    }

    function showValidationErrors(errors) {
        clearValidationErrors();

        Object.keys(errors).forEach(field => {
            let $field = $('#class' + field.charAt(0).toUpperCase() + field.slice(1).replace('_', ''));
            if ($field.length === 0) {
                $field = $('#' + field);
            }

            if ($field.length > 0) {
                $field.addClass('border-red-500 focus:border-red-500 focus:ring-red-500');

                // Add error message
                let errorHtml = '<p class="mt-1 text-sm text-red-600 validation-error">' + errors[field] + '</p>';
                $field.closest('.relative, div').append(errorHtml);
            }
        });
    }

    function clearValidationErrors() {
        $('.border-red-500').removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
        $('.validation-error').remove();
    }

    // Teacher dropdown loading
    function loadTeachersDropdown(selectedTeacherId = null) {
        $.ajax({
            url: '<?= base_url('admin/teachers/dropdown') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                let $select = $('#classTeacher');
                $select.html('<option value="">Select Teacher</option>');

                if (response.success && response.data) {
                    response.data.forEach(teacher => {
                        let selected = selectedTeacherId && selectedTeacherId == teacher.id ? 'selected' : '';
                        $select.append(`<option value="${teacher.id}" ${selected}>${teacher.name}</option>`);
                    });
                }
            },
            error: function() {
                console.warn('Failed to load teachers dropdown');
            }
        });
    }

    // Enhanced Alert System
    function showAlert(type, message, duration = 5000) {
        // Remove existing alerts
        $('.custom-alert').remove();

        let alertClass = {
            'success': 'bg-green-100 border-green-500 text-green-700',
            'error': 'bg-red-100 border-red-500 text-red-700',
            'warning': 'bg-yellow-100 border-yellow-500 text-yellow-700',
            'info': 'bg-blue-100 border-blue-500 text-blue-700'
        } [type] || 'bg-gray-100 border-gray-500 text-gray-700';

        let iconSvg = {
            'success': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>',
            'error': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>',
            'warning': '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>',
            'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>'
        } [type] || '';

        let alertHtml = `
        <div class="custom-alert fixed top-4 right-4 z-50 max-w-md w-full mx-auto">
            <div class="border-l-4 p-4 rounded-lg shadow-lg ${alertClass} animate-slide-in">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="$(this).closest('.custom-alert').remove()" class="inline-flex text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        $('body').append(alertHtml);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                $('.custom-alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);
        }
    }

    // Utility Functions
    function formatDateTime(dateTimeString) {
        try {
            let date = new Date(dateTimeString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return dateTimeString;
        }
    }

    function formatNumber(number) {
        return new Intl.NumberFormat().format(number);
    }

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // ESC to close modal
        if (e.key === 'Escape') {
            if (!$('#classModal').hasClass('hidden')) {
                closeClassModal();
            }
        }

        // Ctrl+N to create new class
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            openCreateClassModal();
        }

        // Ctrl+R to refresh table
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshTable();
        }
    });

    // Enhanced search functionality
    let searchTimeout;
    $(document).on('input', '.dataTables_filter input', function() {
        clearTimeout(searchTimeout);
        let searchTerm = $(this).val();

        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                // Search is automatically handled by DataTables
                console.log('Searching for:', searchTerm);
            }
        }, 300);
    });

    // Real-time form updates
    $(document).on('input', '#className', function() {
        let name = $(this).val();
        $('#classNameDisplay').text(name || 'Create a new class');
        $('#classInitial').text(name ? name.charAt(0).toUpperCase() : 'C');
    });

    $(document).on('change', '#classLevel', function() {
        let level = $(this).val();
        if (level) {
            $('#classLevelDisplay').text('Grade ' + level).show();
        } else {
            $('#classLevelDisplay').hide();
        }
    });

    $(document).on('input', '#classCapacity', function() {
        let capacity = parseInt($(this).val()) || 0;
        let studentCount = parseInt($('#totalStudents').text()) || 0;
        let usage = capacity > 0 ? Math.round((studentCount / capacity) * 100) : 0;
        $('#capacityUsage').text(usage + '%');
    });

    // Enhanced responsive handling
    $(window).on('resize', function() {
        if (classesTable) {
            classesTable.columns.adjust().responsive.recalc();
        }
    });

    console.log('Enhanced Classes Management System initialized successfully');
</script>

<!-- Enhanced CSS for animations and custom styling -->
<style>
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .progress-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        border-radius: 0 0 0.5rem 0.5rem;
        transition: width 1s ease-in-out;
    }

    /* Enhanced DataTables styling */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
        transition: all 0.15s ease-in-out;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Custom checkbox styling */
    .class-checkbox:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Enhanced modal backdrop */
    #classModal {
        backdrop-filter: blur(4px);
    }

    /* Loading overlay */
    #table-loading {
        backdrop-filter: blur(2px);
    }

    /* Responsive table enhancements */
    @media (max-width: 768px) {
        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>