<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle text-primary-600 mr-3"></i>
                    Tambah Kelas Baru
                </h1>
                <p class="text-gray-600 mt-1">Buat kelas baru untuk siswa</p>
            </div>
            <a href="/admin/classes" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form id="createClassForm" action="/admin/classes" method="POST" class="p-6 space-y-6">
            <?= csrf_field() ?>

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Contoh: XI IPA 1" required>
                        <p class="text-sm text-gray-500 mt-1">Nama kelas harus unik</p>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Kelas <span class="text-red-500">*</span>
                        </label>
                        <select id="level" name="level"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                        <div class="invalid-feedback" id="level-error"></div>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapasitas Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="capacity" name="capacity" min="1" max="50"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Maksimal siswa" required>
                        <p class="text-sm text-gray-500 mt-1">Maksimal 50 siswa per kelas</p>
                        <div class="invalid-feedback" id="capacity-error"></div>
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select id="academic_year" name="academic_year"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <option value="2024/2025" selected>2024/2025</option>
                            <option value="2025/2026">2025/2026</option>
                        </select>
                        <div class="invalid-feedback" id="academic_year-error"></div>
                    </div>
                </div>
            </div>

            <!-- Homeroom Teacher -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Wali Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="homeroom_teacher_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Wali Kelas
                        </label>
                        <select id="homeroom_teacher_id" name="homeroom_teacher_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Belum ditentukan</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Opsional - dapat diatur kemudian</p>
                        <div class="invalid-feedback" id="homeroom_teacher_id-error"></div>
                    </div>

                    <div id="teacherInfo" class="hidden">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Informasi Guru</h4>
                            <div id="teacherDetails" class="text-sm text-blue-800"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                <div class="space-y-4">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Kelas
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Deskripsi singkat tentang kelas ini..."></textarea>
                        <p class="text-sm text-gray-500 mt-1">Maksimal 500 karakter</p>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan kelas setelah dibuat</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">Kelas yang tidak aktif tidak akan muncul dalam pilihan pendaftaran siswa</p>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div id="classPreview" class="bg-gray-50 rounded-lg p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div><strong>Nama:</strong> <span id="preview-name">-</span></div>
                        <div><strong>Tingkat:</strong> <span id="preview-level">-</span></div>
                        <div><strong>Kapasitas:</strong> <span id="preview-capacity">-</span> siswa</div>
                    </div>
                    <div class="space-y-2">
                        <div><strong>Tahun Ajaran:</strong> <span id="preview-academic-year">-</span></div>
                        <div><strong>Wali Kelas:</strong> <span id="preview-teacher">-</span></div>
                        <div><strong>Status:</strong> <span id="preview-status">-</span></div>
                    </div>
                </div>
                <div class="mt-4">
                    <strong>Deskripsi:</strong>
                    <p id="preview-description" class="text-gray-600 mt-1">-</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="showPreview()" class="btn btn-outline-secondary">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                    <button type="button" onclick="validateForm()" class="btn btn-outline-primary">
                        <i class="fas fa-check-circle mr-2"></i>
                        Validasi
                    </button>
                </div>

                <div class="flex gap-3">
                    <a href="/admin/classes" class="btn btn-outline-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Kelas
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>
            Panduan Membuat Kelas
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-blue-800">
            <div>
                <h4 class="font-medium mb-2">Penamaan Kelas</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Gunakan format yang konsisten (misal: XI IPA 1)</li>
                    <li>Nama harus unik dalam sistem</li>
                    <li>Hindari karakter khusus berlebihan</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Kapasitas Siswa</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Sesuaikan dengan kapasitas ruang kelas</li>
                    <li>Maksimal 50 siswa per kelas</li>
                    <li>Dapat diubah kemudian jika diperlukan</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Wali Kelas</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Tidak wajib diisi saat membuat kelas</li>
                    <li>Hanya guru aktif yang dapat dipilih</li>
                    <li>Satu guru dapat menjadi wali beberapa kelas</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2">Status Kelas</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Kelas aktif muncul dalam pilihan siswa</li>
                    <li>Kelas tidak aktif disembunyikan</li>
                    <li>Status dapat diubah kapan saja</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        loadTeachers();
        setupFormValidation();
        setupFormPreview();
    });

    function loadTeachers() {
        $('#homeroom_teacher_id').append('<option value="">Loading...</option>');

        fetch('/admin/classes/teachers')
            .then(response => response.json())
            .then(data => {
                $('#homeroom_teacher_id').empty().append('<option value="">Belum ditentukan</option>');

                data.teachers.forEach(teacher => {
                    $('#homeroom_teacher_id').append(
                        `<option value="${teacher.id}" data-email="${teacher.email}">${teacher.name}</option>`
                    );
                });
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
                $('#homeroom_teacher_id').empty().append('<option value="">Error loading teachers</option>');
            });
    }

    function setupFormValidation() {
        // Real-time validation
        $('#name').on('blur', function() {
            validateField('name', this.value);
        });

        $('#capacity').on('input', function() {
            if (this.value > 50) {
                this.value = 50;
            }
            if (this.value < 1) {
                this.value = '';
            }
        });

        $('#description').on('input', function() {
            const maxLength = 500;
            const currentLength = this.value.length;

            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }

            // Update character count
            let counter = $(this).siblings('.char-counter');
            if (counter.length === 0) {
                counter = $('<div class="char-counter text-sm text-gray-500 mt-1"></div>');
                $(this).after(counter);
            }
            counter.text(`${this.value.length}/${maxLength} karakter`);
        });

        // Teacher selection
        $('#homeroom_teacher_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (this.value) {
                const teacherName = selectedOption.text();
                const teacherEmail = selectedOption.data('email');

                $('#teacherDetails').html(`
                <div><strong>Nama:</strong> ${teacherName}</div>
                <div><strong>Email:</strong> ${teacherEmail}</div>
            `);
                $('#teacherInfo').removeClass('hidden');
            } else {
                $('#teacherInfo').addClass('hidden');
            }
        });
    }

    function setupFormPreview() {
        // Update preview on form changes
        $('#name, #level, #capacity, #academic_year, #homeroom_teacher_id, #description, #is_active').on('input change', function() {
            if ($('#classPreview').is(':visible')) {
                updatePreview();
            }
        });
    }

    function validateField(fieldName, value) {
        if (fieldName === 'name' && value) {
            fetch('/admin/classes/check-name', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        name: value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const errorDiv = $(`#${fieldName}-error`);
                    if (!data.available) {
                        errorDiv.text('Nama kelas sudah digunakan').show();
                        $(`#${fieldName}`).addClass('border-red-300');
                    } else {
                        errorDiv.hide();
                        $(`#${fieldName}`).removeClass('border-red-300');
                    }
                })
                .catch(error => console.error('Error validating field:', error));
        }
    }

    function validateForm() {
        const form = $('#createClassForm')[0];
        let isValid = true;

        // Clear previous errors
        $('.invalid-feedback').hide();
        $('.border-red-300').removeClass('border-red-300');

        // Required field validation
        const requiredFields = ['name', 'level', 'capacity', 'academic_year'];
        requiredFields.forEach(field => {
            const input = $(`#${field}`);
            if (!input.val()) {
                input.addClass('border-red-300');
                $(`#${field}-error`).text('Field ini wajib diisi').show();
                isValid = false;
            }
        });

        // Custom validations
        if ($('#capacity').val() > 50) {
            $('#capacity').addClass('border-red-300');
            $('#capacity-error').text('Kapasitas maksimal 50 siswa').show();
            isValid = false;
        }

        if ($('#description').val().length > 500) {
            $('#description').addClass('border-red-300');
            $('#description-error').text('Deskripsi maksimal 500 karakter').show();
            isValid = false;
        }

        if (isValid) {
            showNotification('Validasi berhasil! Form siap untuk disimpan.', 'success');
        } else {
            showNotification('Terdapat kesalahan dalam form. Silakan perbaiki.', 'error');
        }

        return isValid;
    }

    function showPreview() {
        updatePreview();
        $('#classPreview').removeClass('hidden');

        // Scroll to preview
        $('#classPreview')[0].scrollIntoView({
            behavior: 'smooth'
        });
    }

    function updatePreview() {
        $('#preview-name').text($('#name').val() || '-');
        $('#preview-level').text($('#level').val() ? `Kelas ${$('#level').val()}` : '-');
        $('#preview-capacity').text($('#capacity').val() || '-');
        $('#preview-academic-year').text($('#academic_year').val() || '-');
        $('#preview-teacher').text($('#homeroom_teacher_id option:selected').text() || 'Belum ditentukan');
        $('#preview-status').html($('#is_active').is(':checked') ?
            '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Aktif</span>' :
            '<span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Tidak Aktif</span>'
        );
        $('#preview-description').text($('#description').val() || 'Tidak ada deskripsi');
    }

    // Form submission
    $('#createClassForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

        const formData = new FormData(this);

        fetch('/admin/classes', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Kelas berhasil dibuat!', 'success');
                    setTimeout(() => {
                        window.location.href = '/admin/classes';
                    }, 1500);
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            $(`#${field}`).addClass('border-red-300');
                            $(`#${field}-error`).text(data.errors[field]).show();
                        });
                    }
                    showNotification(data.message || 'Terjadi kesalahan saat menyimpan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan sistem', 'error');
            })
            .finally(() => {
                submitBtn.prop('disabled', false).html(originalText);
            });
    });

    function showNotification(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' :
            type === 'error' ? 'bg-red-50 border-red-200' :
            'bg-blue-50 border-blue-200';
        const textColor = type === 'success' ? 'text-green-800' :
            type === 'error' ? 'text-red-800' :
            'text-blue-800';
        const iconClass = type === 'success' ? 'fa-check-circle text-green-500' :
            type === 'error' ? 'fa-exclamation-circle text-red-500' :
            'fa-info-circle text-blue-500';

        const notification = $(`
        <div class="fixed top-4 right-4 z-50 p-4 ${bgColor} border rounded-lg shadow-lg max-w-sm" style="display: none;">
            <div class="flex items-center">
                <i class="fas ${iconClass} mr-2"></i>
                <span class="${textColor}">${message}</span>
            </div>
        </div>
    `);

        $('body').append(notification);
        notification.fadeIn().delay(3000).fadeOut(function() {
            $(this).remove();
        });
    }
</script>
<?= $this->endSection() ?>