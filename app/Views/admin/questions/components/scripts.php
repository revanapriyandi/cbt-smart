<script>
    let questionsTable;

    $(document).ready(function() {
        // Initialize DataTable
        questionsTable = $('#questions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/questions/get-data',
                type: 'GET',
                data: function(d) {
                    d.bank_id = $('#bank-filter').val();
                    d.subject_id = $('#subject-filter').val();
                    d.exam_type_id = $('#exam-type-filter').val();
                    d.difficulty = $('#difficulty-filter').val();
                    d.status = $('#status-filter').val();
                }
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="question-checkbox rounded border-gray-300" value="${data}">`;
                    }
                },
                {
                    data: 'question_text',
                    render: function(data, type, row) {
                        return `<div class="max-w-xs truncate" title="${data}">${data}</div>`;
                    }
                },
                {
                    data: 'question_type',
                    render: function(data, type, row) {
                        const types = {
                            'multiple_choice': 'Pilihan Ganda',
                            'essay': 'Essay',
                            'true_false': 'Benar/Salah',
                            'fill_blank': 'Isian'
                        };
                        return `<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">${types[data] || data}</span>`;
                    }
                },
                {
                    data: 'difficulty_level',
                    render: function(data, type, row) {
                        const colors = {
                            'easy': 'bg-green-100 text-green-800',
                            'medium': 'bg-yellow-100 text-yellow-800',
                            'hard': 'bg-red-100 text-red-800'
                        };
                        const labels = {
                            'easy': 'Mudah',
                            'medium': 'Sedang',
                            'hard': 'Sulit'
                        };
                        return `<span class="px-2 py-1 text-xs font-medium ${colors[data]} rounded-full">${labels[data] || data}</span>`;
                    }
                },
                {
                    data: 'points',
                    render: function(data, type, row) {
                        return `<span class="font-medium">${data}</span>`;
                    }
                },
                {
                    data: 'bank_name',
                    render: function(data, type, row) {
                        return `<div class="text-sm">
                        <div class="font-medium text-gray-900">${data}</div>
                        <div class="text-gray-500">${row.subject_name}</div>
                    </div>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        const color = data === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                        const label = data === 'active' ? 'Aktif' : 'Tidak Aktif';
                        return `<span class="px-2 py-1 text-xs font-medium ${color} rounded-full">${label}</span>`;
                    }
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [7, 'desc']
            ],
            pageLength: 25,
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"flex items-center gap-2"l><"flex-1"<"flex justify-center sm:justify-end"f>>>rtip',
            language: {
                search: '',
                searchPlaceholder: 'Cari soal...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                },
                processing: 'Memuat...'
            },
            drawCallback: function() {
                updateBulkDeleteButton();
            }
        });

        // Select all checkbox
        $('#select-all').change(function() {
            $('.question-checkbox').prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Individual checkbox change
        $(document).on('change', '.question-checkbox', function() {
            updateBulkDeleteButton();
            updateSelectAllCheckbox();
        });

        // Load statistics
        loadStatistics();

        // PDF upload form
        $('#pdf-upload-form').submit(function(e) {
            e.preventDefault();
            handlePdfUpload();
        });
    });

    function loadStatistics() {
        // This would typically come from an AJAX call to get question statistics
        // For now, we'll use the DataTable info
        questionsTable.on('draw', function() {
            const info = questionsTable.page.info();
            $('#total-questions').text(info.recordsTotal);

            // You could add more specific AJAX calls here for other statistics
            // For example: active questions, multiple choice questions, etc.
        });
    }

    function applyFilters() {
        questionsTable.ajax.reload();
    }

    function resetFilters() {
        $('#bank-filter').val('');
        $('#subject-filter').val('');
        $('#exam-type-filter').val('');
        $('#difficulty-filter').val('');
        $('#status-filter').val('');
        questionsTable.ajax.reload();
    }

    function editQuestion(id) {
        window.location.href = `/admin/questions/edit/${id}`;
    }

    function deleteQuestion(id) {
        if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
            $.ajax({
                url: `/admin/questions/delete/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        questionsTable.ajax.reload();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menghapus soal');
                }
            });
        }
    }

    function duplicateQuestion(id) {
        if (confirm('Apakah Anda yakin ingin menduplikasi soal ini?')) {
            $.ajax({
                url: `/admin/questions/duplicate/${id}`,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        questionsTable.ajax.reload();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menduplikasi soal');
                }
            });
        }
    }

    function bulkDelete() {
        const selectedIds = $('.question-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            showAlert('warning', 'Pilih soal yang ingin dihapus');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} soal yang dipilih?`)) {
            $.ajax({
                url: '/admin/questions/bulk-delete',
                type: 'POST',
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        questionsTable.ajax.reload();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menghapus soal');
                }
            });
        }
    }

    function updateBulkDeleteButton() {
        const checkedCount = $('.question-checkbox:checked').length;
        const bulkDeleteBtn = $('#bulk-delete-btn');

        if (checkedCount > 0) {
            bulkDeleteBtn.prop('disabled', false).text(`Hapus ${checkedCount} Terpilih`);
        } else {
            bulkDeleteBtn.prop('disabled', true).text('Hapus Terpilih');
        }
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.question-checkbox').length;
        const checkedCheckboxes = $('.question-checkbox:checked').length;

        $('#select-all').prop('checked', totalCheckboxes > 0 && checkedCheckboxes === totalCheckboxes);
    }

    function uploadPdf() {
        $('#pdf-upload-modal').removeClass('hidden');
    }

    function closePdfModal() {
        $('#pdf-upload-modal').addClass('hidden');
        $('#pdf-upload-form')[0].reset();
        $('#upload-progress').addClass('hidden');
    }

    function handlePdfUpload() {
        const formData = new FormData($('#pdf-upload-form')[0]);

        $('#upload-progress').removeClass('hidden');

        $.ajax({
            url: '/admin/questions/upload-pdf',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#upload-progress').addClass('hidden');

                if (response.success) {
                    showAlert('success', `${response.message}. ${response.questions_extracted} soal berhasil diekstrak.`);
                    closePdfModal();
                    questionsTable.ajax.reload();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                $('#upload-progress').addClass('hidden');
                showAlert('error', 'Terjadi kesalahan saat mengupload PDF');
            }
        });
    }

    function showAlert(type, message) {
        // Create alert element
        const alertClass = {
            'success': 'bg-green-100 border-green-400 text-green-700',
            'error': 'bg-red-100 border-red-400 text-red-700',
            'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info': 'bg-blue-100 border-blue-400 text-blue-700'
        };

        const alertHtml = `
        <div class="fixed top-4 right-4 z-50 ${alertClass[type]} border px-4 py-3 rounded-lg shadow-lg max-w-md">
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold">&times;</button>
            </div>
        </div>
    `;

        $('body').append(alertHtml);

        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.fixed.top-4.right-4').last().fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
</script>