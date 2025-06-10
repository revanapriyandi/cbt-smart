<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="<?= base_url('admin/dashboard') ?>" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li>
                    <a href="<?= base_url('admin/exam-sessions') ?>" class="text-gray-500 hover:text-gray-700">Sesi Ujian</a>
                </li>
                <li>
                    <span class="text-gray-400">/</span>
                </li>
                <li class="text-gray-900 font-medium">Edit Sesi</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg">
                            <i class="fas fa-edit text-white text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Sesi Ujian</h1>
                        <p class="text-gray-600 mt-1">Perbarui informasi sesi ujian: <?= esc($examSession['session_name']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="<?= base_url('admin/exam-sessions') ?>"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Edit Sesi Ujian</h2>
                        <p class="text-blue-100 text-sm">Perbarui informasi sesi ujian yang sudah ada</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="<?= base_url('admin/exam-sessions/update/' . $examSession['id']) ?>" class="p-8" id="editSessionForm">
                <?= csrf_field() ?>

                <!-- Current Status Display -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span class="text-sm font-medium text-gray-700">Status Sesi:</span>
                            <?php
                            $statusColors = [
                                'scheduled' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$examSession['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                <?= ucfirst($examSession['status']) ?>
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-users mr-1"></i>
                            <?= $examSession['participant_count'] ?? 0 ?> peserta terdaftar
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- Section 1: Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Session Name -->
                            <div class="md:col-span-2">
                                <label for="session_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2 text-gray-400"></i>
                                    Nama Sesi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="session_name" name="session_name"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="Contoh: Ujian Tengah Semester - Kelas 10A"
                                    value="<?= old('session_name', $examSession['session_name']) ?>" required>
                                <?php if (isset($errors['session_name'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['session_name'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Exam Selection -->
                            <div>
                                <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-file-alt mr-2 text-gray-400"></i>
                                    Ujian <span class="text-red-500">*</span>
                                </label>
                                <select id="exam_id" name="exam_id"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                                    <option value="">Pilih Ujian</option>
                                    <?php foreach ($exams as $exam): ?>
                                        <option value="<?= $exam['id'] ?>"
                                            data-duration="<?= $exam['duration_minutes'] ?>"
                                            <?= old('exam_id', $examSession['exam_id']) == $exam['id'] ? 'selected' : '' ?>>
                                            <?= esc($exam['title']) ?> (<?= $exam['duration_minutes'] ?> menit)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['exam_id'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['exam_id'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Class Selection -->
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-users mr-2 text-gray-400"></i>
                                    Kelas <span class="text-red-500">*</span>
                                </label>
                                <select id="class_id" name="class_id"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['id'] ?>" <?= old('class_id', $examSession['class_id']) == $class['id'] ? 'selected' : '' ?>>
                                            <?= esc($class['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['class_id'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['class_id'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Schedule & Settings -->
                    <div class="bg-blue-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-blue-600 text-xs"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Jadwal & Pengaturan</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Time -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-play mr-2 text-gray-400"></i>
                                    Waktu Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="start_time" name="start_time"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    value="<?= old('start_time', date('Y-m-d\TH:i', strtotime($examSession['start_time']))) ?>" required>
                                <?php if (isset($errors['start_time'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['start_time'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- End Time -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-stop mr-2 text-gray-400"></i>
                                    Waktu Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="end_time" name="end_time"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    value="<?= old('end_time', date('Y-m-d\TH:i', strtotime($examSession['end_time']))) ?>" required>
                                <?php if (isset($errors['end_time'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['end_time'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Max Participants -->
                            <div>
                                <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-friends mr-2 text-gray-400"></i>
                                    Maksimal Peserta <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="max_participants" name="max_participants" min="1" max="100"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="30"
                                    value="<?= old('max_participants', $examSession['max_participants']) ?>" required>
                                <p class="text-xs text-gray-500 mt-1">Jumlah maksimal siswa yang dapat mengikuti sesi ini</p>
                                <?php if (isset($errors['max_participants'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['max_participants'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Room Location -->
                            <div>
                                <label for="room_location" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                    Lokasi Ruangan
                                </label>
                                <input type="text" id="room_location" name="room_location"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="Lab Komputer 1, Gedung A Lantai 2"
                                    value="<?= old('room_location', $examSession['room_location']) ?>">
                                <p class="text-xs text-gray-500 mt-1">Opsional - Lokasi fisik pelaksanaan ujian</p>
                                <?php if (isset($errors['room_location'])): ?>
                                    <p class="text-red-500 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <?= $errors['room_location'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Security Settings -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    <i class="fas fa-shield-alt mr-2 text-gray-400"></i>
                                    Tingkat Keamanan
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 hover:shadow-md transition-all duration-200 <?= old('security_settings', $examSession['security_settings']) === 'relaxed' ? 'border-green-500 bg-green-50 shadow-md' : 'border-gray-300 hover:border-green-400' ?>">
                                        <input type="radio" name="security_settings" value="relaxed" class="sr-only" <?= old('security_settings', $examSession['security_settings']) === 'relaxed' ? 'checked' : '' ?>>
                                        <div class="flex flex-col items-center text-center w-full">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-unlock text-green-600 text-lg"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">Santai</span>
                                            <span class="text-xs text-gray-500 mt-1">Monitoring minimal</span>
                                            <span class="text-xs text-green-600 mt-1">Cocok untuk latihan</span>
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 hover:shadow-md transition-all duration-200 <?= old('security_settings', $examSession['security_settings']) === 'normal' ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-300 hover:border-blue-400' ?>">
                                        <input type="radio" name="security_settings" value="normal" class="sr-only" <?= old('security_settings', $examSession['security_settings']) === 'normal' ? 'checked' : '' ?>>
                                        <div class="flex flex-col items-center text-center w-full">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-lock text-blue-600 text-lg"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">Normal</span>
                                            <span class="text-xs text-gray-500 mt-1">Monitoring standar</span>
                                            <span class="text-xs text-blue-600 mt-1">Direkomendasikan</span>
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 hover:shadow-md transition-all duration-200 <?= old('security_settings', $examSession['security_settings']) === 'strict' ? 'border-red-500 bg-red-50 shadow-md' : 'border-gray-300 hover:border-red-400' ?>">
                                        <input type="radio" name="security_settings" value="strict" class="sr-only" <?= old('security_settings', $examSession['security_settings']) === 'strict' ? 'checked' : '' ?>>
                                        <div class="flex flex-col items-center text-center w-full">
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-shield-alt text-red-600 text-lg"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">Ketat</span>
                                            <span class="text-xs text-gray-500 mt-1">Monitoring ketat</span>
                                            <span class="text-xs text-red-600 mt-1">Untuk ujian resmi</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Additional Settings -->
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-green-600 text-xs"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Pengaturan Tambahan</h3>
                        </div>

                        <!-- Instructions -->
                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard mr-2 text-gray-400"></i>
                                Instruksi Khusus
                            </label>
                            <textarea id="instructions" name="instructions" rows="4"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                placeholder="Instruksi khusus untuk peserta ujian (opsional)&#10;&#10;Contoh:&#10;- Bawa alat tulis dan kalkulator&#10;- Dilarang membawa HP&#10;- Datang 15 menit sebelum ujian dimulai"><?= old('instructions', $examSession['instructions']) ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Instruksi tambahan yang akan ditampilkan kepada peserta</p>
                            <?php if (isset($errors['instructions'])): ?>
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?= $errors['instructions'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Pastikan semua perubahan sudah benar sebelum menyimpan
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="<?= base_url('admin/exam-sessions') ?>"
                            class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Perbarui Sesi Ujian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Enhanced form interactions for edit form
    document.addEventListener('DOMContentLoaded', function() {
        const examSelect = document.getElementById('exam_id');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const form = document.getElementById('editSessionForm');

        // Auto-calculate end time based on exam duration
        function calculateEndTime() {
            if (examSelect.value && startTimeInput.value) {
                const selectedOption = examSelect.options[examSelect.selectedIndex];
                const duration = selectedOption.getAttribute('data-duration');

                if (duration) {
                    const startTime = new Date(startTimeInput.value);
                    const endTime = new Date(startTime.getTime() + (parseInt(duration) * 60000));

                    // Format datetime-local value
                    const year = endTime.getFullYear();
                    const month = String(endTime.getMonth() + 1).padStart(2, '0');
                    const day = String(endTime.getDate()).padStart(2, '0');
                    const hours = String(endTime.getHours()).padStart(2, '0');
                    const minutes = String(endTime.getMinutes()).padStart(2, '0');

                    endTimeInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;

                    // Show notification
                    showNotification('Waktu selesai otomatis disesuaikan dengan durasi ujian', 'info');
                }
            }
        }

        // Event listeners
        examSelect.addEventListener('change', calculateEndTime);
        startTimeInput.addEventListener('change', calculateEndTime);

        // Radio button styling
        const radioInputs = document.querySelectorAll('input[name="security_settings"]');
        radioInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Remove selected styling from all labels
                radioInputs.forEach(radio => {
                    const label = radio.closest('label');
                    label.classList.remove('border-green-500', 'bg-green-50', 'border-blue-500', 'bg-blue-50', 'border-red-500', 'bg-red-50', 'shadow-md');
                    label.classList.add('border-gray-300');
                });

                // Add selected styling to current label
                const selectedLabel = this.closest('label');
                selectedLabel.classList.remove('border-gray-300');
                selectedLabel.classList.add('shadow-md');

                if (this.value === 'relaxed') {
                    selectedLabel.classList.add('border-green-500', 'bg-green-50');
                } else if (this.value === 'normal') {
                    selectedLabel.classList.add('border-blue-500', 'bg-blue-50');
                } else if (this.value === 'strict') {
                    selectedLabel.classList.add('border-red-500', 'bg-red-50');
                }
            });
        });

        // Form validation with better UX
        form.addEventListener('submit', function(e) {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);

            // Validate times
            if (endTime <= startTime) {
                e.preventDefault();
                showNotification('Waktu selesai harus lebih besar dari waktu mulai', 'error');
                endTimeInput.focus();
                return false;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitBtn.disabled = true;

            // Re-enable if form submission fails
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    });

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;

        const colors = {
            'info': 'bg-blue-500 text-white',
            'success': 'bg-green-500 text-white',
            'error': 'bg-red-500 text-white',
            'warning': 'bg-yellow-500 text-white'
        };

        notification.className += ` ${colors[type] || colors.info}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>

<?= $this->endSection() ?>