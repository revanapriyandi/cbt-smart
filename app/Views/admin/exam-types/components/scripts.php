<script>
    let examTypeTable;
    $(function() {
        // Initialize DataTable
        examTypeTable = $('#examTypesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('admin/exam-types/getData') ?>',
                type: 'POST'
            },
            columns: [{
                data: null,
                orderable: false,
                className: 'text-center',
                width: '50px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="row-select rounded border-gray-300 text-blue-600" value="${row.id}">`;
                }
            }, {
                data: 'name',
                className: 'font-medium text-gray-900',
                render: function(data, type, row) {
                    return `
                            <div class="flex items-center py-2">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 leading-5">${data}</div>
                                    ${row.description ? `<div class="text-xs text-gray-500 mt-1 leading-4">${row.description.substring(0, 60)}${row.description.length > 60 ? '...' : ''}</div>` : '<div class="text-xs text-gray-400 mt-1">Tidak ada deskripsi</div>'}
                                </div>
                            </div>
                        `;
                }
            }, {
                data: 'category',
                className: 'text-center',
                width: '130px',
                render: function(data) {
                    const categories = {
                        'daily': {
                            name: 'Harian',
                            color: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            icon: '📅',
                            bgGradient: 'from-emerald-50 to-green-100'
                        },
                        'mid_semester': {
                            name: 'UTS',
                            color: 'bg-amber-100 text-amber-800 border-amber-200',
                            icon: '📝',
                            bgGradient: 'from-amber-50 to-yellow-100'
                        },
                        'final_semester': {
                            name: 'UAS',
                            color: 'bg-rose-100 text-rose-800 border-rose-200',
                            icon: '🎓',
                            bgGradient: 'from-rose-50 to-red-100'
                        },
                        'national': {
                            name: 'UN',
                            color: 'bg-purple-100 text-purple-800 border-purple-200',
                            icon: '🏛️',
                            bgGradient: 'from-purple-50 to-violet-100'
                        },
                        'practice': {
                            name: 'Latihan',
                            color: 'bg-blue-100 text-blue-800 border-blue-200',
                            icon: '💪',
                            bgGradient: 'from-blue-50 to-indigo-100'
                        },
                        'simulation': {
                            name: 'Simulasi',
                            color: 'bg-slate-100 text-slate-800 border-slate-200',
                            icon: '🎯',
                            bgGradient: 'from-slate-50 to-gray-100'
                        }
                    };
                    const category = categories[data] || {
                        name: data,
                        color: 'bg-gray-100 text-gray-800 border-gray-200',
                        icon: '📋',
                        bgGradient: 'from-gray-50 to-slate-100'
                    };
                    return `
                            <div class="flex justify-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border ${category.color} shadow-sm">
                                    <span class="text-sm mr-1.5">${category.icon}</span>
                                    ${category.name}
                                </span>
                            </div>
                        `;
                }
            }, {
                data: 'duration_minutes',
                className: 'text-center font-medium',
                width: '110px',
                render: function(data) {
                    const hours = Math.floor(data / 60);
                    const minutes = data % 60;
                    let timeStr = '';
                    if (hours > 0) timeStr += `${hours}j `;
                    timeStr += `${minutes}m`;

                    // Color coding based on duration
                    let colorClass = 'text-blue-600 bg-blue-50 border-blue-200';
                    if (data <= 30) colorClass = 'text-green-600 bg-green-50 border-green-200';
                    else if (data <= 90) colorClass = 'text-blue-600 bg-blue-50 border-blue-200';
                    else if (data <= 180) colorClass = 'text-amber-600 bg-amber-50 border-amber-200';
                    else colorClass = 'text-red-600 bg-red-50 border-red-200';

                    return `
                            <div class="flex justify-center">
                                <div class="px-3 py-2 rounded-lg border ${colorClass} shadow-sm">
                                    <div class="text-sm font-bold">${timeStr}</div>
                                    <div class="text-xs opacity-75">${data} menit</div>
                                </div>
                            </div>
                        `;
                }
            }, {
                data: 'status',
                className: 'text-center',
                width: '100px',
                render: function(data) {
                    if (data === 'active') {
                        return `
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></div>
                                        Aktif
                                    </span>
                                </div>
                            `;
                    } else {
                        return `
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-800 border border-red-200 shadow-sm">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        Nonaktif
                                    </span>
                                </div>
                            `;
                    }
                }
            }, {
                data: 'created_at',
                className: 'text-center text-sm text-gray-600',
                width: '130px',
                render: function(data) {
                    const date = new Date(data);
                    const now = new Date();
                    const diffTime = Math.abs(now - date);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    let timeAgo = '';
                    if (diffDays === 1) timeAgo = 'Hari ini';
                    else if (diffDays <= 7) timeAgo = `${diffDays} hari lalu`;
                    else if (diffDays <= 30) timeAgo = `${Math.ceil(diffDays/7)} minggu lalu`;
                    else timeAgo = `${Math.ceil(diffDays/30)} bulan lalu`;

                    return `
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-900">${date.toLocaleDateString('id-ID')}</div>
                                <div class="text-xs text-gray-500 mt-1">${timeAgo}</div>
                            </div>
                        `;
                }
            }, {
                data: 'id',
                orderable: false,
                className: 'text-center',
                width: '140px',
                render: function(data, type, row) {
                    return `
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="editExamType(${data})" class="group inline-flex items-center px-3 py-2 border border-blue-200 text-xs font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-200 shadow-sm">
                                    <svg class="h-3.5 w-3.5 mr-1.5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button onclick="deleteExamType(${data})" class="group inline-flex items-center px-3 py-2 border border-red-200 text-xs font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all duration-200 shadow-sm">
                                    <svg class="h-3.5 w-3.5 mr-1.5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        `;
                }
            }],
            order: [
                [1, 'asc']
            ],
            pageLength: 25,
            responsive: true,
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data di tabel",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"<"mb-3 sm:mb-0"l><"flex items-center space-x-3"f>>rtip',
            drawCallback: function(settings) {
                // Update total records counter
                const api = this.api();
                $('#totalRecords').text(api.page.info().recordsTotal.toLocaleString());

                // Enhanced pagination styling
                $('.dataTables_paginate .paginate_button').addClass('px-4 py-2 mx-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm');
                $('.dataTables_paginate .paginate_button.current').removeClass('text-gray-700 bg-white border-gray-300').addClass('bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700 shadow-md');
                $('.dataTables_paginate .paginate_button.disabled').addClass('opacity-50 cursor-not-allowed hover:bg-white hover:border-gray-300 hover:text-gray-700');

                // Style length menu
                $('.dataTables_length select').addClass('px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500');

                // Style search input
                $('.dataTables_filter input').addClass('px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm').attr('placeholder', 'Cari jenis ujian...');

                // Add row hover effects
                $('#examTypesTable tbody tr').addClass('hover:bg-gray-50 transition-colors duration-150');

                // Add alternating row colors
                $('#examTypesTable tbody tr:even').addClass('bg-gray-25');
                $('#examTypesTable tbody tr:odd').addClass('bg-white');
            }
        });

        // Filter functionality
        $('#searchFilter').on('keyup', function() {
            examTypeTable.search(this.value).draw();
        });

        $('#statusFilter').on('change', function() {
            examTypeTable.column(4).search(this.value).draw();
        });

        $('#categoryFilter').on('change', function() {
            examTypeTable.column(2).search(this.value).draw();
        });

        // Refresh table
        $('#refreshTable').on('click', function() {
            examTypeTable.ajax.reload();
            showNotification('success', 'Data berhasil diperbarui');
        });

        // Select all checkbox
        $('#selectAll').on('change', function() {
            $('.row-select').prop('checked', this.checked);
        });

        // Bulk actions
        $('#executeBulkAction').on('click', function() {
            const action = $('#bulkAction').val();
            const selectedIds = $('.row-select:checked').map(function() {
                return this.value;
            }).get();

            if (!action) {
                showNotification('error', 'Pilih aksi yang ingin dilakukan');
                return;
            }

            if (selectedIds.length === 0) {
                showNotification('error', 'Pilih minimal satu data');
                return;
            }

            const actionText = {
                'activate': 'mengaktifkan',
                'deactivate': 'menonaktifkan',
                'delete': 'menghapus'
            };

            if (confirm(`Apakah Anda yakin ingin ${actionText[action]} ${selectedIds.length} data yang dipilih?`)) {
                $.post('<?= base_url('admin/exam-types/bulk-action') ?>', {
                    action: action,
                    ids: selectedIds
                }, function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        examTypeTable.ajax.reload();
                        $('#selectAll').prop('checked', false);
                        $('#bulkAction').val('');
                    } else {
                        showNotification('error', response.message);
                    }
                }, 'json');
            }
        });

        // Form submission
        $('#examTypeForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const examTypeId = $('#examTypeId').val();
            const url = examTypeId ?
                '<?= base_url('admin/exam-types/update') ?>/' + examTypeId :
                '<?= base_url('admin/exam-types/store') ?>';

            // Debug: Log form data
            console.log('Form data:', Object.fromEntries(formData));
            console.log('URL:', url);

            $('#submitBtn').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.success) {
                        showNotification('success', response.message);
                        closeModal();
                        examTypeTable.ajax.reload();
                    } else {
                        console.log('Error response:', response);
                        const errorMsg = response.message || 'Terjadi kesalahan';
                        const errors = response.errors ? '\n' + Object.values(response.errors).join('\n') : '';
                        showNotification('error', errorMsg + errors);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    console.log('Status:', status);
                    console.log('Error:', error);

                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Server error: ' + xhr.status;
                        }
                    }
                    showNotification('error', errorMessage);
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false).text('Simpan');
                }
            });
        });
    });

    // Modal functions
    function openCreateModal() {
        $('#modalTitle').text('Tambah Jenis Ujian');
        $('#examTypeId').val('');
        $('#examTypeForm')[0].reset();
        $('#examTypeModal').removeClass('hidden');
    }

    function editExamType(id) {
        $('#modalTitle').text('Edit Jenis Ujian');
        $('#examTypeId').val(id);

        // Get exam type data via API
        $.get('<?= base_url('admin/exam-types/get') ?>/' + id, function(response) {
            if (response.success) {
                const data = response.data;
                $('#name').val(data.name);
                $('#category').val(data.category);
                $('#duration_minutes').val(data.duration_minutes);
                $('#status').val(data.status);
                $('#description').val(data.description || '');
                $('#instructions').val(data.instructions || '');
                $('#passing_score').val(data.passing_score || '');
                $('#max_attempts').val(data.max_attempts || '');

                // Set checkboxes
                $('#show_result_immediately').prop('checked', data.show_result_immediately == 1);
                $('#allow_review').prop('checked', data.allow_review == 1);
                $('#randomize_questions').prop('checked', data.randomize_questions == 1);
                $('#randomize_options').prop('checked', data.randomize_options == 1);
                $('#auto_submit').prop('checked', data.auto_submit == 1);
            } else {
                showNotification('error', response.message);
            }
        }, 'json');

        $('#examTypeModal').removeClass('hidden');
    }

    function deleteExamType(id) {
        if (confirm('Hapus Jenis Ujian?\n\nData yang dihapus tidak dapat dikembalikan!')) {
            $.post('<?= base_url('admin/exam-types/delete') ?>/' + id, function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    examTypeTable.ajax.reload();
                } else {
                    showNotification('error', response.message);
                }
            }, 'json');
        }
    }

    function closeModal() {
        $('#examTypeModal').addClass('hidden');
        $('#examTypeForm')[0].reset();
    } // Close modal when clicking outside
    $(document).on('click', function(e) {
        if (e.target.id === 'examTypeModal') {
            closeModal();
        }
    });

    // Custom notification function for exam types
    function showNotification(type, message) {
        // Remove existing notifications
        $('.exam-type-notification').remove();

        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ?
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

        const notification = $(`
            <div class="exam-type-notification fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 max-w-sm">
                <div class="flex-shrink-0">${icon}</div>
                <div class="flex-1 text-sm font-medium">${message}</div>
                <button onclick="$(this).parent().fadeOut(300)" class="ml-2 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `);

        $('body').append(notification);

        // Auto hide after 3 seconds
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
</script>