<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Schedule Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header with Statistics -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Schedule Management</h1>
                <p class="mt-2 text-sm lg:text-base text-gray-600">Comprehensive schedule management with detailed analytics</p>
            </div>
            <button onclick="openCreateModal()" class="mt-4 lg:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Schedule
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Schedules</p>
                        <p class="text-2xl font-semibold text-gray-900" id="totalCount"><?= $stats['total'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Schedules</p>
                        <p class="text-2xl font-semibold text-gray-900" id="activeCount"><?= $stats['active'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Schedules</p>
                        <p class="text-2xl font-semibold text-gray-900" id="todayCount"><?= $stats['today'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">This Week</p>
                        <p class="text-2xl font-semibold text-gray-900" id="weekCount"><?= $stats['this_week'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Schedule List</h3>
                </div>
                <div class="mt-4 sm:mt-0 flex flex-wrap items-center gap-2">
                    <button type="button" class="hidden bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm" id="remove-actions">
                        <i class="fas fa-trash-alt mr-1"></i> Delete Selected
                    </button>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm" onclick="openWeeklyScheduleModal()">
                        <i class="fas fa-calendar-week mr-1"></i> Weekly Schedule
                    </button>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <div class="py-1">
                                <a href="javascript:void(0)" onclick="exportData('excel')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-excel mr-2 text-green-600"></i> Export Excel
                                </a>
                                <a href="javascript:void(0)" onclick="exportData('pdf')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-pdf mr-2 text-red-600"></i> Export PDF
                                </a>
                                <hr class="my-1">
                                <a href="javascript:void(0)" onclick="refreshTable()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sync mr-2 text-blue-600"></i> Refresh
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/schedules/create') ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm flex items-center">
                        <i class="fas fa-plus mr-1"></i> Add Schedule
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="academicYearFilter">
                        <option value="">All Academic Years</option>
                        <?php if (isset($academicYears)): ?>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year['id'] ?>"><?= $year['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="classFilter">
                        <option value="">All Classes</option>
                        <?php if (isset($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>"><?= $class['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="subjectFilter">
                        <option value="">All Subjects</option>
                        <?php if (isset($subjects)): ?>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="teacherFilter">
                        <option value="">All Teachers</option>
                        <?php if (isset($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= $teacher['full_name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="schedulesTable" class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-300 px-4 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" id="checkAll">
                            </th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="schedulesTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Schedule Modal -->
<div id="weeklyScheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden" x-data="{ open: false }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-screen overflow-y-auto">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Weekly Schedule</h3>
                    <button onclick="closeWeeklyScheduleModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="weeklyClassFilter">
                            <option value="">Select Class</option>
                            <?php if (isset($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= $class['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Teacher</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="weeklyTeacherFilter">
                            <option value="">Select Teacher</option>
                            <?php if (isset($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= $teacher['full_name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="loadWeeklySchedule()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">
                            <i class="fas fa-search mr-2"></i> Show Schedule
                        </button>
                    </div>
                </div>
                <div id="weeklyScheduleContent">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                        <p>Select class or teacher to view weekly schedule</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        loadSchedules();

        // Filter handlers
        $('#classFilter, #subjectFilter, #teacherFilter, #statusFilter, #academicYearFilter').change(function() {
            loadSchedules();
        });

        // Check all handler
        $('#checkAll').change(function() {
            $('.schedule-checkbox').prop('checked', this.checked);
            toggleBulkActions();
        });

        // Individual checkbox handler
        $(document).on('change', '.schedule-checkbox', function() {
            toggleBulkActions();

            var totalCheckboxes = $('.schedule-checkbox').length;
            var checkedCheckboxes = $('.schedule-checkbox:checked').length;

            $('#checkAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            $('#checkAll').prop('checked', checkedCheckboxes === totalCheckboxes);
        });

        // Bulk actions
        $('#remove-actions').click(function() {
            var selected = getSelectedSchedules();
            if (selected.length === 0) {
                alert('Please select schedules first');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selected.length} selected schedules?`)) {
                bulkDelete(selected);
            }
        });
    });

    function loadSchedules() {
        // Show loading state
        $('#schedulesTableBody').html('<tr><td colspan="10" class="text-center py-4">Loading...</td></tr>');

        $.ajax({
            url: '<?= base_url('admin/schedules/getData') ?>',
            type: 'POST',
            data: {
                class_filter: $('#classFilter').val(),
                subject_filter: $('#subjectFilter').val(),
                teacher_filter: $('#teacherFilter').val(),
                status_filter: $('#statusFilter').val(),
                academic_year_filter: $('#academicYearFilter').val()
            },
            success: function(response) {
                if (response.data) {
                    displaySchedules(response.data);
                } else {
                    showError('Failed to load schedules');
                }
            },
            error: function() {
                showError('System error occurred');
            }
        });
    }

    function displaySchedules(schedules) {
        var html = '';

        if (schedules.length === 0) {
            html = '<tr><td colspan="10" class="text-center py-4 text-gray-500">No schedules found</td></tr>';
        } else {
            schedules.forEach(function(schedule) {
                var statusBadge = schedule.is_active == 1 ?
                    '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Active</span>' :
                    '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Inactive</span>';

                html += `
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-3">
                        <input type="checkbox" class="schedule-checkbox rounded border-gray-300 text-blue-600" value="${schedule.id}">
                    </td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.academic_year || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.class_name || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.subject_name || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.teacher_name || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.day_of_week || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.time_range || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${schedule.room || '-'}</td>
                    <td class="border border-gray-300 px-4 py-3">${statusBadge}</td>
                    <td class="border border-gray-300 px-4 py-3">
                        <div class="flex space-x-2">
                            <a href="<?= base_url('admin/schedules/view/') ?>${schedule.id}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('admin/schedules/edit/') ?>${schedule.id}" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteSchedule(${schedule.id})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        }

        $('#schedulesTableBody').html(html);
    }

    function toggleBulkActions() {
        var selected = $('.schedule-checkbox:checked').length;
        if (selected > 0) {
            $('#remove-actions').removeClass('hidden');
        } else {
            $('#remove-actions').addClass('hidden');
        }
    }

    function getSelectedSchedules() {
        return $('.schedule-checkbox:checked').map(function() {
            return this.value;
        }).get();
    }

    function deleteSchedule(id) {
        if (confirm('Are you sure you want to delete this schedule?')) {
            $.ajax({
                url: '<?= base_url('admin/schedules/delete/') ?>' + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        loadSchedules();
                    } else {
                        showError(response.message);
                    }
                },
                error: function() {
                    showError('System error occurred');
                }
            });
        }
    }

    function bulkDelete(ids) {
        $.ajax({
            url: '<?= base_url('admin/schedules/bulkAction') ?>',
            type: 'POST',
            data: {
                action: 'delete',
                ids: ids
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message);
                    loadSchedules();
                    $('#checkAll').prop('checked', false);
                    toggleBulkActions();
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('System error occurred');
            }
        });
    }

    function exportData(format) {
        window.open('<?= base_url('admin/schedules/export') ?>?format=' + format, '_blank');
    }

    function refreshTable() {
        loadSchedules();
        showSuccess('Data refreshed successfully');
    }

    function openWeeklyScheduleModal() {
        document.getElementById('weeklyScheduleModal').classList.remove('hidden');
    }

    function closeWeeklyScheduleModal() {
        document.getElementById('weeklyScheduleModal').classList.add('hidden');
    }

    function loadWeeklySchedule() {
        var classId = $('#weeklyClassFilter').val();
        var teacherId = $('#weeklyTeacherFilter').val();

        if (!classId && !teacherId) {
            alert('Please select class or teacher first');
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/schedules/getWeeklySchedule') ?>',
            type: 'GET',
            data: {
                class_id: classId,
                teacher_id: teacherId
            },
            success: function(response) {
                if (response.success) {
                    displayWeeklySchedule(response.data);
                } else {
                    showError('Failed to load weekly schedule');
                }
            },
            error: function() {
                showError('System error occurred');
            }
        });
    }

    function displayWeeklySchedule(schedules) {
        var days = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        var html = '<div class="overflow-x-auto"><table class="w-full border-collapse border border-gray-300">';
        html += '<thead class="bg-gray-50"><tr><th class="border border-gray-300 px-4 py-3 text-left font-medium text-gray-900">Time</th>';

        for (var i = 1; i <= 7; i++) {
            html += '<th class="border border-gray-300 px-4 py-3 text-center font-medium text-gray-900">' + days[i] + '</th>';
        }
        html += '</tr></thead><tbody>';

        // Get all unique time slots
        var timeSlots = [];
        for (var day in schedules) {
            if (schedules[day]) {
                schedules[day].forEach(function(schedule) {
                    var timeSlot = schedule.start_time + ' - ' + schedule.end_time;
                    if (timeSlots.indexOf(timeSlot) === -1) {
                        timeSlots.push(timeSlot);
                    }
                });
            }
        }

        timeSlots.sort();

        timeSlots.forEach(function(timeSlot) {
            html += '<tr><td class="border border-gray-300 px-4 py-3 font-medium">' + timeSlot + '</td>';

            for (var day = 1; day <= 7; day++) {
                var daySchedules = schedules[day] || [];
                var scheduleForTime = daySchedules.find(function(s) {
                    return (s.start_time + ' - ' + s.end_time) === timeSlot;
                });

                if (scheduleForTime) {
                    html += '<td class="border border-gray-300 px-4 py-3 text-center">';
                    html += '<div class="bg-blue-100 p-2 rounded">';
                    html += '<div class="font-medium text-blue-900">' + scheduleForTime.subject_name + '</div>';
                    html += '<div class="text-sm text-blue-700">' + scheduleForTime.class_name + '</div>';
                    html += '<div class="text-sm text-blue-600">' + scheduleForTime.teacher_name + '</div>';
                    if (scheduleForTime.room) {
                        html += '<div class="text-xs text-blue-600">Room: ' + scheduleForTime.room + '</div>';
                    }
                    html += '</div></td>';
                } else {
                    html += '<td class="border border-gray-300 px-4 py-3"></td>';
                }
            }
            html += '</tr>';
        });

        html += '</tbody></table></div>';

        if (timeSlots.length === 0) {
            html = '<div class="text-center text-gray-500 py-8">';
            html += '<i class="fas fa-calendar-alt text-4xl mb-4"></i>';
            html += '<p>No schedules found</p>';
            html += '</div>';
        }

        $('#weeklyScheduleContent').html(html);
    }

    function openCreateModal() {
        window.location.href = '<?= base_url('admin/schedules/create') ?>';
    }

    function showSuccess(message) {
        // Simple alert for now - you can replace with toast notifications
        alert('Success: ' + message);
    }

    function showError(message) {
        // Simple alert for now - you can replace with toast notifications
        alert('Error: ' + message);
    }

    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('[id$="Count"]');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const increment = target / 100;
            let current = 0;

            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        });
    });
</script>
<?= $this->endSection() ?>