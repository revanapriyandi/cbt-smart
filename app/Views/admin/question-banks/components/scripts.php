<!-- Custom CSS for DataTables with Tailwind -->
<style>
    /* DataTables custom styling for Tailwind CSS */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.25rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_info {
        font-size: 0.875rem;
        color: #4b5563;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        text-decoration: none;
        color: #374151;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #f9fafb;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #9ca3af;
        cursor: not-allowed;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background-color: white;
    }

    /* Remove default DataTables margins */
    .dataTables_wrapper {
        margin: 0 !important;
    }

    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        margin: 0.5rem 0 !important;
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

<script>
    $(document).ready(function() {
        let questionBanksTable;
        let isEditing = false;
        let currentId = null;

        // Initialize DataTable
        function initDataTable() {
            questionBanksTable = $('#questionBanksTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url('admin/question-banks/data') ?>',
                    type: 'POST',
                    data: function(d) {
                        console.log(d)
                        d.subject_filter = $('#subject_filter').val();
                        d.exam_type_filter = $('#exam_type_filter').val();
                        d.difficulty_filter = $('#difficulty_filter').val();
                        d.status_filter = $('#status_filter').val();
                        d.created_by_filter = $('#created_by_filter').val();
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <div class="text-center">
                                <div class="flex items-center justify-center">
                                    <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded row-checkbox" type="checkbox" value="${data}" id="check_${data}">
                                    <label class="ml-2 text-sm text-gray-500" for="check_${data}">
                                        #${data}
                                    </label>
                                </div>
                            </div>
                        `;
                        }
                    },
                    {
                        data: 'name',
                        render: function(data, type, row) {
                            return `
                            <div class="flex items-center">
                                <div class="mr-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-database text-white text-sm"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">${data}</div>
                                    ${row.description ? `<div class="text-sm text-gray-500">${row.description.substring(0, 50)}...</div>` : ''}
                                </div>
                            </div>
                        `;
                        }
                    },
                    {
                        data: 'subject_name',
                        render: function(data, type, row) {
                            return data ? `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                                <i class="fas fa-book mr-1"></i>
                                ${data}
                            </span>
                        ` : '<span class="text-gray-400">-</span>';
                        }
                    },
                    {
                        data: 'exam_type_name',
                        render: function(data, type, row) {
                            return data ? `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                ${data}
                            </span>
                        ` : '<span class="text-gray-400">-</span>';
                        }
                    },
                    {
                        data: 'difficulty_level',
                        render: function(data, type, row) {
                            const levels = {
                                'easy': {
                                    class: 'bg-green-100 text-green-800',
                                    icon: 'smile',
                                    text: 'Mudah'
                                },
                                'medium': {
                                    class: 'bg-yellow-100 text-yellow-800',
                                    icon: 'meh',
                                    text: 'Sedang'
                                },
                                'hard': {
                                    class: 'bg-red-100 text-red-800',
                                    icon: 'frown',
                                    text: 'Sulit'
                                }
                            };
                            const level = levels[data] || {
                                class: 'bg-gray-100 text-gray-800',
                                icon: 'question',
                                text: data
                            };
                            return `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${level.class}">
                                <i class="fas fa-${level.icon} mr-1"></i>
                                ${level.text}
                            </span>
                        `;
                        }
                    },
                    {
                        data: 'question_count',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <div class="flex items-center justify-center">
                                <span class="w-6 h-6 bg-gray-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-2">
                                    ${data}
                                </span>
                                <span class="text-sm text-gray-500">soal</span>
                            </div>
                        `;
                        }
                    },
                    {
                        data: 'used_count',
                        className: 'text-center',
                        render: function(data, type, row) {
                            const percentage = row.question_count > 0 ? Math.round((data / row.question_count) * 100) : 0;
                            return `
                            <div class="flex items-center justify-center">
                                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-2">
                                    ${data}
                                </span>
                                <span class="text-sm text-gray-500">(${percentage}%)</span>
                            </div>
                        `;
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            const statuses = {
                                'active': {
                                    class: 'bg-green-100 text-green-800',
                                    icon: 'check-circle',
                                    text: 'Aktif'
                                },
                                'draft': {
                                    class: 'bg-yellow-100 text-yellow-800',
                                    icon: 'edit',
                                    text: 'Draft'
                                },
                                'archived': {
                                    class: 'bg-gray-100 text-gray-800',
                                    icon: 'archive',
                                    text: 'Arsip'
                                }
                            };
                            const status = statuses[data] || {
                                class: 'bg-gray-100 text-gray-800',
                                icon: 'question',
                                text: data
                            };
                            return `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${status.class}">
                                <i class="fas fa-${status.icon} mr-1"></i>
                                ${status.text}
                            </span>
                        `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <div class="flex space-x-1 justify-center">
                                <button type="button" class="bg-cyan-500 hover:bg-cyan-600 text-white px-2 py-1 rounded text-xs transition-colors" onclick="viewQuestionBank(${row.id})" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors" onclick="editQuestionBank(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition-colors" onclick="manageQuestions(${row.id})" title="Kelola Soal">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors" onclick="deleteQuestionBank(${row.id})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                language: {
                    url: '<?= base_url('assets/datatables/i18n/id.json') ?>'
                },
                drawCallback: function() {
                    // Update bulk action visibility
                    updateBulkActionVisibility();
                }
            });
        }

        // Initialize components
        initDataTable();

        // Filter change handlers
        $('#filter-form select').on('change', function() {
            questionBanksTable.ajax.reload();
        });

        // Reset filters
        $('#btn-reset-filter').on('click', function() {
            $('#filter-form')[0].reset();
            questionBanksTable.ajax.reload();
        });

        // Row checkbox handlers
        $(document).on('change', '.row-checkbox', function() {
            updateBulkActionVisibility();
        });

        // Select all checkbox
        $(document).on('change', '#select-all', function() {
            $('.row-checkbox').prop('checked', this.checked);
            updateBulkActionVisibility();
        });

        // Update bulk action visibility
        function updateBulkActionVisibility() {
            const checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                $('.bulk-actions').show();
                $('.text-muted').text(`${checkedCount} item dipilih`);
            } else {
                $('.bulk-actions').hide();
                $('.text-muted').text('Pilih item untuk melakukan aksi bulk');
            }
        } // Create button
        $('#btn-create').on('click', function() {
            resetForm();
            isEditing = false;
            currentId = null;
            $('#modal-title').text('Tambah Bank Soal');
            $('#btn-save-text').text('Simpan');
            openModal('questionBankModal');
        });

        // Form submission
        $('#questionBankForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = isEditing ?
                `<?= base_url('admin/question-banks/update') ?>/${currentId}` :
                '<?= base_url('admin/question-banks/store') ?>';

            // Show loading state
            $('#btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        closeModal('questionBankModal');
                        showNotification('success', response.message);
                        questionBanksTable.ajax.reload();
                    } else {
                        showNotification('error', response.message || 'Terjadi kesalahan saat menyimpan data');

                        // Show validation errors
                        if (response.errors) {
                            Object.keys(response.errors).forEach(function(field) {
                                const input = $(`#${field}`);
                                input.addClass('border-red-500');
                                input.siblings('.text-red-500').text(response.errors[field]).removeClass('hidden');
                            });
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showNotification('error', 'Terjadi kesalahan saat menyimpan data');
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html('<i class="fas fa-save me-1"></i><span id="btn-save-text">' + (isEditing ? 'Update' : 'Simpan') + '</span>');
                }
            });
        }); // Reset form
        function resetForm() {
            $('#questionBankForm')[0].reset();
            $('#questionBankForm .border-red-500').removeClass('border-red-500');
            $('#questionBankForm .text-red-500').addClass('hidden').text('');
            currentId = null;
        }

        // View question bank details
        window.viewQuestionBank = function(id) {
            $.ajax({
                url: `<?= base_url('admin/question-banks/view') ?>/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#view-content').html(response.html);
                        openModal('viewModal');
                    } else {
                        showNotification('error', response.message || 'Gagal memuat detail bank soal');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showNotification('error', 'Terjadi kesalahan saat memuat detail');
                }
            });
        };

        // Edit question bank
        window.editQuestionBank = function(id) {
            $.ajax({
                url: `<?= base_url('admin/question-banks/edit') ?>/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Fill form fields
                        $('#questionbank_id').val(data.id);
                        $('#name').val(data.name);
                        $('#subject_id').val(data.subject_id);
                        $('#exam_type_id').val(data.exam_type_id);
                        $('#difficulty_level').val(data.difficulty_level);
                        $('#description').val(data.description);
                        $('#instructions').val(data.instructions);
                        $('#time_per_question').val(data.time_per_question);
                        $('#negative_marks').val(data.negative_marks);
                        $('#tags').val(data.tags);
                        $('#status').val(data.status);

                        // Handle checkboxes
                        $('#negative_marking').prop('checked', data.negative_marking == 1);
                        $('#randomize_questions').prop('checked', data.randomize_questions == 1);
                        $('#show_correct_answer').prop('checked', data.show_correct_answer == 1);
                        $('#allow_calculator').prop('checked', data.allow_calculator == 1);
                        // Set modal state
                        isEditing = true;
                        currentId = id;
                        $('#modal-title').text('Edit Bank Soal');
                        $('#btn-save-text').text('Update');
                        openModal('questionBankModal');

                    } else {
                        showNotification('error', response.message || 'Gagal memuat data bank soal');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showNotification('error', 'Terjadi kesalahan saat memuat data');
                }
            });
        };

        // Delete question bank
        window.deleteQuestionBank = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus bank soal ini? Semua soal dalam bank soal ini juga akan terhapus.')) {
                $.ajax({
                    url: `<?= base_url('admin/question-banks/delete') ?>/${id}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', response.message);
                            questionBanksTable.ajax.reload();
                        } else {
                            showNotification('error', response.message || 'Gagal menghapus bank soal');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showNotification('error', 'Terjadi kesalahan saat menghapus data');
                    }
                });
            }
        };

        // Manage questions
        window.manageQuestions = function(id) {
            window.location.href = `<?= base_url('admin/questions') ?>?bank_id=${id}`;
        };

        // Bulk actions
        $('#bulk-delete').on('click', function() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (selectedIds.length === 0) {
                showNotification('warning', 'Pilih minimal satu item untuk dihapus');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} bank soal terpilih?`)) {
                $.ajax({
                    url: '<?= base_url('admin/question-banks/bulk-delete') ?>',
                    type: 'POST',
                    data: {
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', response.message);
                            questionBanksTable.ajax.reload();
                            $('.row-checkbox').prop('checked', false);
                            updateBulkActionVisibility();
                        } else {
                            showNotification('error', response.message || 'Gagal menghapus bank soal');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showNotification('error', 'Terjadi kesalahan saat menghapus data');
                    }
                });
            }
        });

        $('#bulk-archive').on('click', function() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (selectedIds.length === 0) {
                showNotification('warning', 'Pilih minimal satu item untuk diarsipkan');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin mengarsipkan ${selectedIds.length} bank soal terpilih?`)) {
                $.ajax({
                    url: '<?= base_url('admin/question-banks/bulk-archive') ?>',
                    type: 'POST',
                    data: {
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', response.message);
                            questionBanksTable.ajax.reload();
                            $('.row-checkbox').prop('checked', false);
                            updateBulkActionVisibility();
                        } else {
                            showNotification('error', response.message || 'Gagal mengarsipkan bank soal');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showNotification('error', 'Terjadi kesalahan saat mengarsipkan data');
                    }
                });
            }
        });

        $('#bulk-activate').on('click', function() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (selectedIds.length === 0) {
                showNotification('warning', 'Pilih minimal satu item untuk diaktifkan');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin mengaktifkan ${selectedIds.length} bank soal terpilih?`)) {
                $.ajax({
                    url: '<?= base_url('admin/question-banks/bulk-activate') ?>',
                    type: 'POST',
                    data: {
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', response.message);
                            questionBanksTable.ajax.reload();
                            $('.row-checkbox').prop('checked', false);
                            updateBulkActionVisibility();
                        } else {
                            showNotification('error', response.message || 'Gagal mengaktifkan bank soal');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showNotification('error', 'Terjadi kesalahan saat mengaktifkan data');
                    }
                });
            }
        });

        // Export functionality
        $('#btn-export').on('click', function() {
            const filters = {
                subject_filter: $('#subject_filter').val(),
                exam_type_filter: $('#exam_type_filter').val(),
                difficulty_filter: $('#difficulty_filter').val(),
                status_filter: $('#status_filter').val(),
                created_by_filter: $('#created_by_filter').val()
            };

            const queryString = new URLSearchParams(filters).toString();
            window.location.href = `<?= base_url('admin/question-banks/export') ?>?${queryString}`;
        });

        // Import functionality
        $('#btn-import').on('click', function() {
            window.location.href = '<?= base_url('admin/question-banks/import') ?>';
        });

        // Form validation reset on input
        $('#questionBankForm input, #questionBankForm select, #questionBankForm textarea').on('input change', function() {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('');
        });

        // Notification function for Tailwind CSS
        function showNotification(type, message) {
            const colors = {
                success: 'bg-green-500 border-green-600',
                error: 'bg-red-500 border-red-600',
                warning: 'bg-yellow-500 border-yellow-600',
                info: 'bg-blue-500 border-blue-600'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const notification = $(`
            <div class="fixed top-4 right-4 z-50 transform transition-all duration-300 translate-x-full">
                <div class="max-w-sm rounded-lg shadow-lg border-l-4 ${colors[type]} bg-white p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas ${icons[type]} text-${type === 'success' ? 'green' : type === 'error' ? 'red' : type === 'warning' ? 'yellow' : 'blue'}-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">${message}</p>
                        </div>
                        <div class="ml-auto">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="$(this).closest('.fixed').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);

            $('body').append(notification);

            // Animate in
            setTimeout(() => {
                notification.removeClass('translate-x-full');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.addClass('translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

        // Add some custom CSS for better styling
        $('<style>')
            .prop('type', 'text/css')
            .html(`
            .icon-circle {
                height: 2.5rem;
                width: 2.5rem;
                border-radius: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.375rem 0.75rem;
                margin-left: 0.125rem;
                border: 1px solid #d1d3e2;
                border-radius: 0.35rem;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #eaecf4;
                border-color: #d1d3e2;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #4e73df !important;
                border-color: #4e73df !important;
                color: white !important;
            }
            
            .table th {
                border-top: none !important;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 1rem 0.75rem;
            }
            
            .table td {
                padding: 1rem 0.75rem;
                vertical-align: middle;
            }
            
            .btn-group .btn {
                margin-right: 2px;
            }
            
            .btn-group .btn:last-child {
                margin-right: 0;
            }
            
            @media (max-width: 768px) {
                .btn-group {
                    display: flex;
                    flex-direction: column;
                }
                
                .btn-group .btn {
                    margin-bottom: 2px;
                    margin-right: 0;
                }
            }
        `)
            .appendTo('head');

        // Modal utility functions for Tailwind CSS
        function openModal(modalId) {
            $(`#${modalId}`).removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeModal(modalId) {
            $(`#${modalId}`).addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        // Global function to close modal (called from button onclick)
        window.closeModal = function() {
            $('#questionBankModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        };

        window.closeViewModal = function() {
            $('#viewModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        };
    });
</script>