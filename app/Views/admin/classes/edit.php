<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit text-primary-600 mr-3"></i>
                    Edit Kelas: <?= esc($class['name']) ?>
                </h1>
                <p class="text-gray-600 mt-1">Ubah informasi kelas</p>
            </div>
            <div class="flex gap-2">
                <a href="/admin/classes/<?= $class['id'] ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="/admin/classes" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form id="editClassForm" action="/admin/classes/<?= $class['id'] ?>" method="POST" class="p-6 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="<?= esc($class['name']) ?>"
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
                            <option value="10" <?= $class['level'] == 10 ? 'selected' : '' ?>>Kelas 10</option>
                            <option value="11" <?= $class['level'] == 11 ? 'selected' : '' ?>>Kelas 11</option>
                            <option value="12" <?= $class['level'] == 12 ? 'selected' : '' ?>>Kelas 12</option>
                        </select>
                        <div class="invalid-feedback" id="level-error"></div>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapasitas Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="capacity" name="capacity" value="<?= esc($class['capacity']) ?>"
                            min="<?= $class['student_count'] ?>" max="50"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Maksimal siswa" required>
                        <p class="text-sm text-gray-500 mt-1">
                            Minimal: <?= $class['student_count'] ?> (jumlah siswa saat ini) | Maksimal: 50
                        </p>
                        <div class="invalid-feedback" id="capacity-error"></div>
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select id="academic_year" name="academic_year"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <option value="2023/2024" <?= $class['academic_year'] == '2023/2024' ? 'selected' : '' ?>>2023/2024</option>
                            <option value="2024/2025" <?= $class['academic_year'] == '2024/2025' ? 'selected' : '' ?>>2024/2025</option>
                            <option value="2025/2026" <?= $class['academic_year'] == '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
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
                        <p class="text-sm text-gray-500 mt-1">Opsional - dapat dikosongkan</p>
                        <div class="invalid-feedback" id="homeroom_teacher_id-error"></div>
                    </div>

                    <div id="teacherInfo" class="hidden">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Informasi Guru</h4>
                            <div id="teacherDetails" class="text-sm text-blue-800"></div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($class['homeroom_teacher_name'])): ?>
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-medium text-yellow-900 mb-2">Wali Kelas Saat Ini</h4>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-yellow-900"><?= esc($class['homeroom_teacher_name']) ?></p>
                                <p class="text-sm text-yellow-700"><?= esc($class['homeroom_teacher_email']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
                            placeholder="Deskripsi singkat tentang kelas ini..."><?= esc($class['description']) ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Maksimal 500 karakter</p>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                <?= $class['is_active'] ? 'checked' : '' ?>
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Kelas aktif</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">Kelas yang tidak aktif tidak akan muncul dalam pilihan pendaftaran siswa</p>

                        <?php if ($class['student_count'] > 0): ?>
                            <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                <p class="text-sm text-orange-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Perhatian: Kelas ini memiliki <?= $class['student_count'] ?> siswa.
                                    Menonaktifkan kelas akan mempengaruhi akses siswa ke ujian dan materi.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Change History -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Perubahan</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Dibuat pada:</p>
                            <p class="font-medium"><?= date('d M Y H:i', strtotime($class['created_at'])) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Terakhir diubah:</p>
                            <p class="font-medium"><?= date('d M Y H:i', strtotime($class['updated_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Changes -->
            <div id="changesPreview" class="bg-blue-50 rounded-lg p-6 hidden">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Preview Perubahan</h3>
                <div id="changesList" class="space-y-2">
                    <!-- Changes will be populated here -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="showChanges()" class="btn btn-outline-secondary">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Perubahan
                    </button>
                    <button type="button" onclick="validateForm()" class="btn btn-outline-primary">
                        <i class="fas fa-check-circle mr-2"></i>
                        Validasi
                    </button>
                </div>

                <div class="flex gap-3">
                    <a href="/admin/classes/<?= $class['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <?php if ($class['student_count'] == 0): ?>
        <div class="bg-white rounded-lg shadow-sm border border-red-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Zona Bahaya
                </h3>
                <p class="text-red-700 mb-4">
                    Tindakan di bawah ini bersifat permanen dan tidak dapat dibatalkan.
                </p>
                <button onclick="deleteClass()" class="btn btn-danger">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Kelas
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Current Students Info -->
    <?php if ($class['student_count'] > 0): ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Siswa</h3>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <div>
                        <p class="text-blue-900 font-medium">Kelas ini memiliki <?= $class['student_count'] ?> siswa</p>
                        <p class="text-blue-700 text-sm">
                            Pastikan perubahan yang Anda lakukan tidak mengganggu proses pembelajaran siswa.
                            <a href="/admin/classes/<?= $class['id'] ?>" class="underline">Lihat daftar siswa</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const originalData = {
        name: <?= json_encode($class['name']) ?>,
        level: <?= json_encode($class['level']) ?>,
        capacity: <?= json_encode($class['capacity']) ?>,
        academic_year: <?= json_encode($class['academic_year']) ?>,
        homeroom_teacher_id: <?= json_encode($class['homeroom_teacher_id']) ?>,
        description: <?= json_encode($class['description']) ?>,
        is_active: <?= $class['is_active'] ? 'true' : 'false' ?>
    };

    $(document).ready(function() {
        loadTeachers();
        setupFormValidation();
        setupChangeDetection();
    });

    function loadTeachers() {
        $('#homeroom_teacher_id').append('<option value="">Loading...</option>');

        fetch('/admin/classes/teachers')
            .then(response => response.json())
            .then(data => {
                $('#homeroom_teacher_id').empty().append('<option value="">Belum ditentukan</option>');

                data.teachers.forEach(teacher => {
                    const selected = teacher.id == <?= $class['homeroom_teacher_id'] ?: 'null' ?> ? 'selected' : '';
                    $('#homeroom_teacher_id').append(
                        `<option value="${teacher.id}" data-email="${teacher.email}" ${selected}>${teacher.name}</option>`
                    );
                });

                // Trigger change to show current teacher info
                if (<?= $class['homeroom_teacher_id'] ?: 'null' ?>) {
                    $('#homeroom_teacher_id').trigger('change');
                }
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
                $('#homeroom_teacher_id').empty().append('<option value="">Error loading teachers</option>');
            });
    }

    function setupFormValidation() {
        // Real-time validation
        $('#name').on('blur', function() {
            if (this.value !== originalData.name) {
                validateField('name', this.value);
            }
        });

        $('#capacity').on('input', function() {
            const currentStudents = <?= $class['student_count'] ?>;
            if (this.value > 50) {
                this.value = 50;
            }
            if (this.value < currentStudents) {
                this.value = currentStudents;
                showNotification(`Kapasitas tidak boleh kurang dari ${currentStudents} (jumlah siswa saat ini)`, 'error');
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

    function setupChangeDetection() {
        // Monitor form changes
        $('#editClassForm input, #editClassForm select, #editClassForm textarea').on('input change', function() {
            detectChanges();
        });
    }

    function detectChanges() {
        const currentData = {
            name: $('#name').val(),
            level: $('#level').val(),
            capacity: $('#capacity').val(),
            academic_year: $('#academic_year').val(),
            homeroom_teacher_id: $('#homeroom_teacher_id').val(),
            description: $('#description').val(),
            is_active: $('#is_active').is(':checked').toString()
        };

        let hasChanges = false;
        Object.keys(originalData).forEach(key => {
            if (originalData[key] != currentData[key]) {
                hasChanges = true;
            }
        });

        // Update submit button state
        if (hasChanges) {
            $('#submitBtn').removeClass('btn-primary').addClass('btn-warning').html('<i class="fas fa-save mr-2"></i>Simpan Perubahan');
        } else {
            $('#submitBtn').removeClass('btn-warning').addClass('btn-primary').html('<i class="fas fa-save mr-2"></i>Simpan Perubahan');
        }
    }

    function showChanges() {
        const currentData = {
            name: $('#name').val(),
            level: $('#level').val(),
            capacity: $('#capacity').val(),
            academic_year: $('#academic_year').val(),
            homeroom_teacher_id: $('#homeroom_teacher_id').val(),
            description: $('#description').val(),
            is_active: $('#is_active').is(':checked').toString()
        };

        let changes = [];

        Object.keys(originalData).forEach(key => {
            if (originalData[key] != currentData[key]) {
                let fieldName = '';
                let oldValue = originalData[key];
                let newValue = currentData[key];

                switch (key) {
                    case 'name':
                        fieldName = 'Nama Kelas';
                        break;
                    case 'level':
                        fieldName = 'Tingkat';
                        oldValue = oldValue ? `Kelas ${oldValue}` : '-';
                        newValue = newValue ? `Kelas ${newValue}` : '-';
                        break;
                    case 'capacity':
                        fieldName = 'Kapasitas';
                        oldValue = oldValue ? `${oldValue} siswa` : '-';
                        newValue = newValue ? `${newValue} siswa` : '-';
                        break;
                    case 'academic_year':
                        fieldName = 'Tahun Ajaran';
                        break;
                    case 'homeroom_teacher_id':
                        fieldName = 'Wali Kelas';
                        oldValue = originalData[key] ? $('#homeroom_teacher_id option[value="' + originalData[key] + '"]').text() : 'Belum ditentukan';
                        newValue = currentData[key] ? $('#homeroom_teacher_id option[value="' + currentData[key] + '"]').text() : 'Belum ditentukan';
                        break;
                    case 'description':
                        fieldName = 'Deskripsi';
                        oldValue = oldValue || 'Kosong';
                        newValue = newValue || 'Kosong';
                        break;
                    case 'is_active':
                        fieldName = 'Status';
                        oldValue = originalData[key] === 'true' ? 'Aktif' : 'Tidak Aktif';
                        newValue = currentData[key] === 'true' ? 'Aktif' : 'Tidak Aktif';
                        break;
                }

                changes.push({
                    field: fieldName,
                    old: oldValue,
                    new: newValue
                });
            }
        });

        if (changes.length > 0) {
            let html = '';
            changes.forEach(change => {
                html += `
                <div class="flex justify-between items-center py-2 border-b border-blue-200 last:border-b-0">
                    <div>
                        <span class="font-medium text-blue-900">${change.field}:</span>
                        <span class="text-blue-700 ml-2">${change.old}</span>
                        <i class="fas fa-arrow-right mx-2 text-blue-500"></i>
                        <span class="text-blue-900 font-medium">${change.new}</span>
                    </div>
                </div>
            `;
            });
            $('#changesList').html(html);
            $('#changesPreview').removeClass('hidden');

            // Scroll to preview
            $('#changesPreview')[0].scrollIntoView({
                behavior: 'smooth'
            });
        } else {
            showNotification('Tidak ada perubahan yang terdeteksi', 'info');
        }
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
                        name: value,
                        exclude_id: <?= $class['id'] ?>
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
        const form = $('#editClassForm')[0];
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
        const currentStudents = <?= $class['student_count'] ?>;
        if ($('#capacity').val() < currentStudents) {
            $('#capacity').addClass('border-red-300');
            $('#capacity-error').text(`Kapasitas tidak boleh kurang dari ${currentStudents} (jumlah siswa saat ini)`).show();
            isValid = false;
        }

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

    function deleteClass() {
        if (confirm('Yakin ingin menghapus kelas ini? Aksi ini tidak dapat dibatalkan.')) {
            if (confirm('Konfirmasi sekali lagi: Hapus kelas <?= esc($class['name']) ?>?')) {
                fetch('/admin/classes/<?= $class['id'] ?>', {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Kelas berhasil dihapus!', 'success');
                            setTimeout(() => {
                                window.location.href = '/admin/classes';
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Terjadi kesalahan saat menghapus', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan sistem', 'error');
                    });
            }
        }
    }

    // Form submission
    $('#editClassForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

        const formData = new FormData(this);

        fetch('/admin/classes/<?= $class['id'] ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Kelas berhasil diperbarui!', 'success');
                    setTimeout(() => {
                        window.location.href = '/admin/classes/<?= $class['id'] ?>';
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

    // Warn user about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        const currentData = {
            name: $('#name').val(),
            level: $('#level').val(),
            capacity: $('#capacity').val(),
            academic_year: $('#academic_year').val(),
            homeroom_teacher_id: $('#homeroom_teacher_id').val(),
            description: $('#description').val(),
            is_active: $('#is_active').is(':checked').toString()
        };

        let hasChanges = false;
        Object.keys(originalData).forEach(key => {
            if (originalData[key] != currentData[key]) {
                hasChanges = true;
            }
        });

        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
<?= $this->endSection() ?>