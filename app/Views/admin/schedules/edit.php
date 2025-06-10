<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900"><?= $title ?></h1>
                <p class="mt-2 text-sm lg:text-base text-gray-600">Update schedule information</p>
            </div>
            <a href="<?= base_url('admin/schedules') ?>" class="mt-4 lg:mt-0 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Schedules
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form id="editScheduleForm" action="<?= base_url('admin/schedules/update/' . $schedule['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                        <select id="academic_year_id" name="academic_year_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Academic Year</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= $year['id'] == $schedule['academic_year_id'] ? 'selected' : '' ?>><?= esc($year['name']) ?><?= $year['is_current'] ? ' (Current)' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Class -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select id="class_id" name="class_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= $class['id'] == $schedule['class_id'] ? 'selected' : '' ?>><?= esc($class['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select id="subject_id" name="subject_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= $subject['id'] == $schedule['subject_id'] ? 'selected' : '' ?>><?= esc($subject['name']) ?> (<?= esc($subject['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Teacher -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                        <select id="teacher_id" name="teacher_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $schedule['teacher_id'] ? 'selected' : '' ?>><?= esc($teacher['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Day of Week -->
                    <div>
                        <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
                        <select id="day_of_week" name="day_of_week" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Day</option>
                            <option value="1" <?= $schedule['day_of_week'] == 1 ? 'selected' : '' ?>>Monday</option>
                            <option value="2" <?= $schedule['day_of_week'] == 2 ? 'selected' : '' ?>>Tuesday</option>
                            <option value="3" <?= $schedule['day_of_week'] == 3 ? 'selected' : '' ?>>Wednesday</option>
                            <option value="4" <?= $schedule['day_of_week'] == 4 ? 'selected' : '' ?>>Thursday</option>
                            <option value="5" <?= $schedule['day_of_week'] == 5 ? 'selected' : '' ?>>Friday</option>
                            <option value="6" <?= $schedule['day_of_week'] == 6 ? 'selected' : '' ?>>Saturday</option>
                            <option value="7" <?= $schedule['day_of_week'] == 7 ? 'selected' : '' ?>>Sunday</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="active" <?= $schedule['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $schedule['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= $schedule['status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="<?= date('H:i', strtotime($schedule['start_time'])) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" id="end_time" name="end_time" value="<?= date('H:i', strtotime($schedule['end_time'])) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Room -->
                    <div>
                        <label for="room" class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                        <input type="text" id="room" name="room" value="<?= esc($schedule['room'] ?? '') ?>" placeholder="Enter room name/number" maxlength="100" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Additional notes (optional)" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?= esc($schedule['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" id="submitBtn">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Schedule
                        </span>
                    </button>
                    <a href="<?= base_url('admin/schedules') ?>" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium text-center focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Updating Schedule</h3>
                <p class="text-sm text-gray-500">Please wait...</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editScheduleForm');
        const submitBtn = document.getElementById('submitBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                submitBtn.disabled = true;
                loadingOverlay.classList.remove('hidden');

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        loadingOverlay.classList.add('hidden');

                        if (data.success) {
                            // Show success message
                            showAlert('Schedule updated successfully!', 'success');
                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = '<?= base_url('admin/schedules') ?>';
                            }, 1500);
                        } else {
                            showAlert(data.message || 'Failed to update schedule', 'error');
                            if (data.errors) {
                                displayValidationErrors(data.errors);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loadingOverlay.classList.add('hidden');
                        showAlert('An error occurred while updating the schedule', 'error');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                    });
            }
        });

        // Form validation
        function validateForm() {
            clearValidationErrors();
            let isValid = true;

            // Required fields
            const requiredFields = ['academic_year_id', 'class_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time', 'status'];

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    showFieldError(field, 'This field is required');
                    isValid = false;
                }
            });

            // Time validation
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime && startTime >= endTime) {
                showFieldError(document.getElementById('end_time'), 'End time must be after start time');
                isValid = false;
            }

            return isValid;
        }

        function showFieldError(field, message) {
            field.classList.add('border-red-500');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-red-500 text-sm mt-1 field-error';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        function clearValidationErrors() {
            document.querySelectorAll('.field-error').forEach(error => error.remove());
            document.querySelectorAll('.border-red-500').forEach(field => {
                field.classList.remove('border-red-500');
            });
        }

        function displayValidationErrors(errors) {
            Object.keys(errors).forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    showFieldError(field, errors[fieldName]);
                }
            });
        }

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';

            alertDiv.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded-lg shadow-lg z-50 max-w-md`;
            alertDiv.innerHTML = `
            <div class="flex items-center">
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-lg leading-none">&times;</button>
            </div>
        `;

            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Auto-calculate duration when times change
        document.getElementById('start_time').addEventListener('change', calculateDuration);
        document.getElementById('end_time').addEventListener('change', calculateDuration);

        function calculateDuration() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime) {
                const start = new Date(`2000-01-01 ${startTime}`);
                const end = new Date(`2000-01-01 ${endTime}`);

                if (end > start) {
                    const duration = (end - start) / (1000 * 60); // Duration in minutes
                    // You can display duration somewhere if needed
                    console.log(`Duration: ${duration} minutes`);
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>