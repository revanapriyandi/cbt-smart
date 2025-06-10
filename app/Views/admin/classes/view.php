<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-users-class text-primary-600 mr-3"></i>
                    Detail Kelas: <?= esc($class['name']) ?>
                </h1>
                <p class="text-gray-600 mt-1">Informasi lengkap dan manajemen kelas</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/admin/classes" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <a href="/admin/classes/<?= $class['id'] ?>/edit" class="btn btn-outline-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <button onclick="toggleClassStatus(<?= $class['id'] ?>, <?= $class['is_active'] ?>)"
                    class="btn btn-<?= $class['is_active'] ? 'outline-warning' : 'outline-success' ?>">
                    <i class="fas fa-<?= $class['is_active'] ? 'ban' : 'check' ?> mr-2"></i>
                    <?= $class['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Class Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nama Kelas</label>
                            <p class="text-lg font-semibold text-gray-900"><?= esc($class['name']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tingkat</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Kelas <?= esc($class['level']) ?>
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tahun Ajaran</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <?= esc($class['academic_year']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Kapasitas</label>
                            <p class="text-lg font-semibold text-gray-900"><?= esc($class['capacity']) ?> siswa</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Jumlah Siswa</label>
                            <div class="flex items-center">
                                <p class="text-lg font-semibold text-gray-900 mr-2"><?= esc($class['student_count']) ?> siswa</p>
                                <?php
                                $percentage = $class['capacity'] > 0 ? round(($class['student_count'] / $class['capacity']) * 100) : 0;
                                $colorClass = $percentage >= 90 ? 'text-red-600' : ($percentage >= 75 ? 'text-yellow-600' : 'text-green-600');
                                ?>
                                <span class="text-sm <?= $colorClass ?>">(<?= $percentage ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-<?= $percentage >= 90 ? 'red' : ($percentage >= 75 ? 'yellow' : 'green') ?>-600 h-2 rounded-full"
                                    style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <?php if ($class['is_active']): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($class['description'])): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Deskripsi</label>
                        <p class="text-gray-700"><?= esc($class['description']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Students List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Siswa</h3>
                    <div class="flex gap-2">
                        <button onclick="addStudent()" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Siswa
                        </button>
                        <button onclick="exportStudents()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="studentSearch" placeholder="Cari siswa..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <select id="studentStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="overflow-x-auto">
                        <table id="studentsTable" class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Homeroom Teacher -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Wali Kelas</h3>
                <?php if (!empty($class['homeroom_teacher_name'])): ?>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900"><?= esc($class['homeroom_teacher_name']) ?></p>
                            <p class="text-sm text-gray-500"><?= esc($class['homeroom_teacher_email']) ?></p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button onclick="changeHomeroom()" class="btn btn-sm btn-outline-primary w-full">
                            <i class="fas fa-edit mr-2"></i>
                            Ganti Wali Kelas
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500 mb-4">Belum ada wali kelas</p>
                        <button onclick="assignHomeroom()" class="btn btn-primary w-full">
                            <i class="fas fa-plus mr-2"></i>
                            Pilih Wali Kelas
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Cepat</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Siswa Aktif</span>
                        <span class="font-semibold text-green-600" id="activeStudents">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Siswa Tidak Aktif</span>
                        <span class="font-semibold text-red-600" id="inactiveStudents">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sisa Kapasitas</span>
                        <span class="font-semibold text-blue-600"><?= $class['capacity'] - $class['student_count'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Ujian Aktif</span>
                        <span class="font-semibold text-purple-600" id="activeExams">-</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
                <div id="recentActivity" class="space-y-3">
                    <!-- Activity items will be loaded via AJAX -->
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button onclick="viewSchedule()" class="btn btn-outline-primary w-full text-left">
                        <i class="fas fa-calendar mr-2"></i>
                        Lihat Jadwal
                    </button>
                    <button onclick="viewExams()" class="btn btn-outline-primary w-full text-left">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Ujian Kelas
                    </button>
                    <button onclick="viewReports()" class="btn btn-outline-primary w-full text-left">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Laporan Kelas
                    </button>
                    <button onclick="manageSubjects()" class="btn btn-outline-primary w-full text-left">
                        <i class="fas fa-book mr-2"></i>
                        Mata Pelajaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Siswa ke Kelas</h3>
        </div>
        <div class="px-6 py-4">
            <div class="mb-4">
                <input type="text" id="searchAvailableStudents" placeholder="Cari siswa yang belum memiliki kelas..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div id="availableStudentsList" class="max-h-96 overflow-y-auto">
                <!-- Available students will be loaded here -->
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
            <button onclick="closeAddStudentModal()" class="btn btn-outline-secondary">Batal</button>
            <button onclick="addSelectedStudents()" class="btn btn-primary">Tambah Siswa Terpilih</button>
        </div>
    </div>
</div>

<!-- Change Homeroom Modal -->
<div id="homeroomModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pilih Wali Kelas</h3>
        </div>
        <div class="px-6 py-4">
            <select id="homeroomTeacherSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Guru...</option>
            </select>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
            <button onclick="closeHomeroomModal()" class="btn btn-outline-secondary">Batal</button>
            <button onclick="saveHomeroom()" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const classId = <?= $class['id'] ?>;

    $(document).ready(function() {
        loadStudents();
        loadQuickStats();
        loadRecentActivity();
    });

    function loadStudents() {
        $('#studentsTable tbody').html('<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>');

        fetch(`/admin/classes/${classId}/students`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (data.students.length > 0) {
                    data.students.forEach((student, index) => {
                        html += `
                        <tr>
                            <td class="px-4 py-3">${index + 1}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${student.name}</div>
                                        <div class="text-sm text-gray-500">${student.email}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">${student.student_id || '-'}</td>
                            <td class="px-4 py-3">
                                ${student.is_active ? 
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>'
                                }
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">${formatDate(student.joined_at)}</td>
                            <td class="px-4 py-3">
                                <button onclick="removeStudent(${student.id})" class="text-red-600 hover:text-red-800" title="Keluarkan dari kelas">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada siswa dalam kelas ini</td></tr>';
                }
                $('#studentsTable tbody').html(html);
            })
            .catch(error => {
                console.error('Error loading students:', error);
                $('#studentsTable tbody').html('<tr><td colspan="6" class="text-center py-4 text-red-500">Error loading students</td></tr>');
            });
    }

    function loadQuickStats() {
        fetch(`/admin/classes/${classId}/stats`)
            .then(response => response.json())
            .then(data => {
                $('#activeStudents').text(data.activeStudents || 0);
                $('#inactiveStudents').text(data.inactiveStudents || 0);
                $('#activeExams').text(data.activeExams || 0);
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    function loadRecentActivity() {
        fetch(`/admin/classes/${classId}/activity`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (data.activities.length > 0) {
                    data.activities.forEach(activity => {
                        html += `
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">${activity.description}</p>
                                <p class="text-xs text-gray-500">${formatDate(activity.created_at)}</p>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    html = '<p class="text-sm text-gray-500 text-center">Belum ada aktivitas</p>';
                }
                $('#recentActivity').html(html);
            })
            .catch(error => console.error('Error loading activity:', error));
    }

    function addStudent() {
        $('#addStudentModal').removeClass('hidden').addClass('flex');
        loadAvailableStudents();
    }

    function loadAvailableStudents() {
        $('#availableStudentsList').html('<div class="text-center py-4">Loading...</div>');

        fetch(`/admin/classes/${classId}/available-students`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (data.students.length > 0) {
                    data.students.forEach(student => {
                        html += `
                        <div class="flex items-center p-3 border border-gray-200 rounded-lg mb-2 hover:bg-gray-50">
                            <input type="checkbox" value="${student.id}" class="available-student-checkbox mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">${student.name}</div>
                                <div class="text-sm text-gray-500">${student.email}</div>
                                ${student.student_id ? `<div class="text-xs text-gray-400">NIS: ${student.student_id}</div>` : ''}
                            </div>
                        </div>
                    `;
                    });
                } else {
                    html = '<div class="text-center py-8 text-gray-500">Tidak ada siswa yang tersedia</div>';
                }
                $('#availableStudentsList').html(html);
            })
            .catch(error => {
                console.error('Error loading available students:', error);
                $('#availableStudentsList').html('<div class="text-center py-4 text-red-500">Error loading students</div>');
            });
    }

    function closeAddStudentModal() {
        $('#addStudentModal').addClass('hidden').removeClass('flex');
    }

    function addSelectedStudents() {
        const selectedIds = [];
        $('.available-student-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Pilih siswa terlebih dahulu');
            return;
        }

        fetch(`/admin/classes/${classId}/add-students`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    student_ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAddStudentModal();
                    loadStudents();
                    loadQuickStats();
                    location.reload(); // Reload to update student count
                } else {
                    showNotification(data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan sistem', 'error');
            });
    }

    function removeStudent(studentId) {
        if (confirm('Yakin ingin mengeluarkan siswa dari kelas ini?')) {
            fetch(`/admin/classes/${classId}/remove-student/${studentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadStudents();
                        loadQuickStats();
                        location.reload(); // Reload to update student count
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan sistem', 'error');
                });
        }
    }

    function assignHomeroom() {
        loadTeachers();
        $('#homeroomModal').removeClass('hidden').addClass('flex');
    }

    function changeHomeroom() {
        loadTeachers();
        $('#homeroomModal').removeClass('hidden').addClass('flex');
    }

    function loadTeachers() {
        $('#homeroomTeacherSelect').html('<option value="">Loading...</option>');

        fetch('/admin/classes/teachers')
            .then(response => response.json())
            .then(data => {
                let html = '<option value="">Pilih Guru...</option>';
                data.teachers.forEach(teacher => {
                    html += `<option value="${teacher.id}">${teacher.name} - ${teacher.email}</option>`;
                });
                $('#homeroomTeacherSelect').html(html);
            })
            .catch(error => {
                console.error('Error loading teachers:', error);
                $('#homeroomTeacherSelect').html('<option value="">Error loading teachers</option>');
            });
    }

    function closeHomeroomModal() {
        $('#homeroomModal').addClass('hidden').removeClass('flex');
    }

    function saveHomeroom() {
        const teacherId = $('#homeroomTeacherSelect').val();

        fetch(`/admin/classes/${classId}/homeroom`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    teacher_id: teacherId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeHomeroomModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan sistem', 'error');
            });
    }

    function toggleClassStatus(id, currentStatus) {
        const action = currentStatus ? 'nonaktifkan' : 'aktifkan';
        if (confirm(`Yakin ingin ${action} kelas ini?`)) {
            fetch(`/admin/classes/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan sistem', 'error');
                });
        }
    }

    function exportStudents() {
        window.open(`/admin/classes/${classId}/export-students`, '_blank');
    }

    function viewSchedule() {
        window.location.href = `/admin/schedules?class=${classId}`;
    }

    function viewExams() {
        window.location.href = `/admin/exams?class=${classId}`;
    }

    function viewReports() {
        window.location.href = `/admin/reports/class/${classId}`;
    }

    function manageSubjects() {
        window.location.href = `/admin/classes/${classId}/subjects`;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

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

    // Search functionality
    $('#studentSearch').on('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        $('#studentsTable tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(searchTerm) > -1);
        });
    });

    $('#studentStatus').on('change', function() {
        const status = this.value;
        if (status === '') {
            $('#studentsTable tbody tr').show();
        } else {
            $('#studentsTable tbody tr').each(function() {
                const statusCell = $(this).find('td:nth-child(4)');
                const isActive = statusCell.find('.bg-green-100').length > 0;
                $(this).toggle((status === '1' && isActive) || (status === '0' && !isActive));
            });
        }
    });

    $('#searchAvailableStudents').on('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        $('#availableStudentsList > div').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(searchTerm) > -1);
        });
    });
</script>
<?= $this->endSection() ?>