<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Take Exam - <?= esc($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50">
    <!-- Exam Header -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center min-w-0 flex-1 mr-4">
                    <h1 class="text-lg font-semibold text-gray-900 truncate"><?= esc($exam['title']) ?></h1>
                    <span class="ml-3 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full hidden sm:block">
                        <?= esc($exam['subject_name']) ?>
                    </span>
                </div>

                <!-- Timer -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="text-sm text-gray-600 hidden sm:block">
                        Time Remaining:
                    </div>
                    <div id="timer" class="bg-red-100 text-red-800 px-2 sm:px-3 py-1 rounded-lg font-mono text-sm sm:text-lg font-bold">
                        --:--:--
                    </div>
                    <button onclick="submitExam()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium text-sm">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6"> <!-- Mobile Question Navigation Toggle -->
        <div class="lg:hidden mb-4">
            <button id="mobileNavToggle" onclick="toggleMobileNav()"
                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 flex items-center justify-between text-left">
                <div class="flex items-center">
                    <span class="font-medium text-gray-900">Question <?= 1 ?> of <?= count($questions) ?></span>
                    <span class="ml-3 text-sm text-gray-500" id="mobile-progress">0/<?= count($questions) ?> answered</span>
                </div>
                <svg id="navToggleIcon" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Question Navigation Panel -->
        <div id="mobileNavPanel" class="lg:hidden hidden mb-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="grid grid-cols-6 sm:grid-cols-8 gap-2 mb-4">
                    <?php for ($i = 1; $i <= count($questions); $i++): ?>
                        <button onclick="goToQuestion(<?= $i ?>); toggleMobileNav();"
                            id="mobile-nav-btn-<?= $i ?>"
                            class="w-10 h-10 text-sm rounded border <?= $i === 1 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-600 mb-3">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-blue-600 rounded mr-1"></div>
                        <span>Current</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-600 rounded mr-1"></div>
                        <span>Answered</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-gray-300 rounded mr-1"></div>
                        <span>Not Answered</span>
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="mobile-progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Desktop Question Navigation Sidebar -->
            <div class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-24">
                    <h3 class="font-semibold text-gray-900 mb-4">Questions</h3>
                    <div class="grid grid-cols-4 gap-2">
                        <?php for ($i = 1; $i <= count($questions); $i++): ?>
                            <button onclick="goToQuestion(<?= $i ?>)"
                                id="nav-btn-<?= $i ?>"
                                class="w-8 h-8 text-xs rounded border <?= $i === 1 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </button>
                        <?php endfor; ?>
                    </div>

                    <div class="mt-6 space-y-2 text-xs">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded mr-2"></div>
                            <span>Current</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-600 rounded mr-2"></div>
                            <span>Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-300 rounded mr-2"></div>
                            <span>Not Answered</span>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Progress</span>
                            <span id="progress-text">0/<?= count($questions) ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div> <!-- Main Exam Content -->
            <div class="lg:col-span-3 w-full">
                <form id="examForm" method="POST" action="<?= base_url('student/exam/submit/' . $exam['id']) ?>">
                    <?php foreach ($questions as $index => $question): ?>
                        <div id="question-<?= $index + 1 ?>" class="question-container bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 <?= $index === 0 ? '' : 'hidden' ?>">
                            <!-- Question Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-2 sm:space-y-0">
                                <div class="flex items-center">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        Question <?= $index + 1 ?> of <?= count($questions) ?>
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Points: <?= $question['points'] ?? 10 ?>
                                </div>
                            </div>

                            <!-- PDF Display (if available) -->
                            <?php if (!empty($question['pdf_url'])): ?>
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-900 mb-3">Reference Material:</h4>
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <iframe src="<?= esc($question['pdf_url']) ?>"
                                            class="w-full h-96"
                                            frameborder="0">
                                            <p>Your browser does not support PDFs.
                                                <a href="<?= esc($question['pdf_url']) ?>" target="_blank">Download the PDF</a>.
                                            </p>
                                        </iframe>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Question Text -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Question:</h4>
                                <div class="prose max-w-none">
                                    <?= nl2br(esc($question['question_text'])) ?>
                                </div>
                            </div>

                            <!-- Answer Area -->
                            <div class="mb-6">
                                <label class="block font-medium text-gray-900 mb-3">Your Answer:</label>
                                <textarea name="answers[<?= $question['id'] ?>]"
                                    id="answer-<?= $question['id'] ?>"
                                    rows="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical"
                                    placeholder="Type your answer here..."></textarea>
                            </div> <!-- Navigation Buttons -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-100 space-y-3 sm:space-y-0">
                                <button type="button"
                                    onclick="previousQuestion()"
                                    id="prev-btn"
                                    class="flex items-center justify-center px-4 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed order-2 sm:order-1"
                                    <?= $index === 0 ? 'disabled' : '' ?>>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </button>

                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 order-1 sm:order-2">
                                    <button type="button"
                                        onclick="saveAndNext()"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-center">
                                        Save & Continue
                                    </button>

                                    <?php if ($index < count($questions) - 1): ?>
                                        <button type="button"
                                            onclick="nextQuestion()"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center">
                                            Next
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    <?php else: ?>
                                        <button type="button"
                                            onclick="submitExam()"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-center">
                                            Submit Exam
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-yellow-100 rounded-full mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Submit Exam</h3>
                    <p class="text-sm text-gray-600">Are you sure you want to submit your exam?</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="text-sm">
                    <div class="flex justify-between mb-2">
                        <span>Total Questions:</span>
                        <span class="font-medium"><?= count($questions) ?></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Answered:</span>
                        <span id="answered-count" class="font-medium">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Unanswered:</span>
                        <span id="unanswered-count" class="font-medium"><?= count($questions) ?></span>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <button onclick="closeSubmitModal()" class="flex-1 px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                    Cancel
                </button>
                <button onclick="confirmSubmit()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentQuestion = 1;
    let totalQuestions = <?= count($questions) ?>;
    let examDuration = <?= $exam['duration'] ?> * 60; // Convert to seconds
    let timeRemaining = examDuration;
    let examTimer;
    let answeredQuestions = new Set(); // Initialize exam
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
        updateProgress();
        loadSavedAnswers();
        updateMobileNavDisplay();
    });

    function toggleMobileNav() {
        const panel = document.getElementById('mobileNavPanel');
        const icon = document.getElementById('navToggleIcon');

        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            panel.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function updateMobileNavDisplay() {
        const mobileToggle = document.getElementById('mobileNavToggle');
        const mobileProgress = document.getElementById('mobile-progress');
        const mobileProgressBar = document.getElementById('mobile-progress-bar');

        if (mobileToggle) {
            const questionText = mobileToggle.querySelector('span:first-child');
            questionText.textContent = `Question ${currentQuestion} of ${totalQuestions}`;
        }

        if (mobileProgress) {
            mobileProgress.textContent = `${answeredQuestions.size}/${totalQuestions} answered`;
        }

        if (mobileProgressBar) {
            const percentage = (answeredQuestions.size / totalQuestions) * 100;
            mobileProgressBar.style.width = `${percentage}%`;
        }

        // Update mobile navigation buttons
        for (let i = 1; i <= totalQuestions; i++) {
            const btn = document.getElementById(`mobile-nav-btn-${i}`);
            if (btn) {
                const questionId = <?= json_encode(array_column($questions, 'id')) ?>[i - 1];
                const answer = document.getElementById(`answer-${questionId}`).value.trim();

                if (i === currentQuestion) {
                    btn.className = 'w-10 h-10 text-sm rounded border bg-blue-600 text-white border-blue-600';
                } else if (answer !== '') {
                    btn.className = 'w-10 h-10 text-sm rounded border bg-green-600 text-white border-green-600';
                } else {
                    btn.className = 'w-10 h-10 text-sm rounded border bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                }
            }
        }
    }

    function startTimer() {
        examTimer = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();

            if (timeRemaining <= 0) {
                clearInterval(examTimer);
                alert('Time is up! Your exam will be submitted automatically.');
                confirmSubmit();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;

        const timerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('timer').textContent = timerText;

        // Change color based on time remaining
        const timer = document.getElementById('timer');
        if (timeRemaining <= 300) { // 5 minutes
            timer.className = 'bg-red-100 text-red-800 px-3 py-1 rounded-lg font-mono text-lg font-bold animate-pulse';
        } else if (timeRemaining <= 900) { // 15 minutes
            timer.className = 'bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg font-mono text-lg font-bold';
        }
    }

    function goToQuestion(questionNum) {
        if (questionNum >= 1 && questionNum <= totalQuestions) {
            // Hide current question
            document.getElementById(`question-${currentQuestion}`).classList.add('hidden');

            // Show target question
            document.getElementById(`question-${questionNum}`).classList.remove('hidden');

            // Update navigation
            updateNavigation(questionNum);

            currentQuestion = questionNum;
            updateNavigationButtons();
        }
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions) {
            goToQuestion(currentQuestion + 1);
        }
    }

    function previousQuestion() {
        if (currentQuestion > 1) {
            goToQuestion(currentQuestion - 1);
        }
    }

    function updateNavigation(activeQuestion) {
        // Update navigation buttons
        for (let i = 1; i <= totalQuestions; i++) {
            const btn = document.getElementById(`nav-btn-${i}`);
            const questionId = <?= json_encode(array_column($questions, 'id')) ?>[i - 1];
            const answer = document.getElementById(`answer-${questionId}`).value.trim();

            if (btn) {
                if (i === activeQuestion) {
                    btn.className = 'w-8 h-8 text-xs rounded border bg-blue-600 text-white border-blue-600';
                } else if (answer !== '') {
                    btn.className = 'w-8 h-8 text-xs rounded border bg-green-600 text-white border-green-600';
                    answeredQuestions.add(i);
                } else {
                    btn.className = 'w-8 h-8 text-xs rounded border bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                }
            }
        }

        updateProgress();
        updateMobileNavDisplay();
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prev-btn');
        if (prevBtn) {
            prevBtn.disabled = currentQuestion === 1;
        }
    }

    function updateProgress() {
        const progressText = document.getElementById('progress-text');
        const progressBar = document.getElementById('progress-bar');

        if (progressText && progressBar) {
            progressText.textContent = `${answeredQuestions.size}/${totalQuestions}`;
            const percentage = (answeredQuestions.size / totalQuestions) * 100;
            progressBar.style.width = `${percentage}%`;
        }
    }

    function saveAndNext() {
        saveCurrentAnswer();
        if (currentQuestion < totalQuestions) {
            nextQuestion();
        }
    }

    function saveCurrentAnswer() {
        const questionIds = <?= json_encode(array_column($questions, 'id')) ?>;
        const questionId = questionIds[currentQuestion - 1];
        const answer = document.getElementById(`answer-${questionId}`).value.trim();

        if (answer !== '') {
            answeredQuestions.add(currentQuestion);
        } else {
            answeredQuestions.delete(currentQuestion);
        }

        updateNavigation(currentQuestion);

        // Auto-save to server
        autoSave(questionId, answer);
    }

    function autoSave(questionId, answer) {
        fetch('<?= base_url('student/exam/autosave/' . $exam['id']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                question_id: questionId,
                answer: answer
            })
        }).catch(error => {
            console.error('Auto-save failed:', error);
        });
    }

    function loadSavedAnswers() {
        fetch('<?= base_url('student/exam/load-answers/' . $exam['id']) ?>')
            .then(response => response.json())
            .then(data => {
                if (data.answers) {
                    data.answers.forEach(answer => {
                        const textarea = document.getElementById(`answer-${answer.question_id}`);
                        if (textarea) {
                            textarea.value = answer.answer_text;
                        }
                    });
                    updateNavigation(currentQuestion);
                }
            })
            .catch(error => {
                console.error('Failed to load saved answers:', error);
            });
    }

    function submitExam() {
        // Save current answer first
        saveCurrentAnswer();

        // Update submission modal
        document.getElementById('answered-count').textContent = answeredQuestions.size;
        document.getElementById('unanswered-count').textContent = totalQuestions - answeredQuestions.size;

        // Show modal
        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function confirmSubmit() {
        clearInterval(examTimer);
        document.getElementById('examForm').submit();
    }

    // Auto-save on input change
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'TEXTAREA') {
            const questionId = e.target.id.replace('answer-', '');
            clearTimeout(e.target.autoSaveTimeout);
            e.target.autoSaveTimeout = setTimeout(() => {
                autoSave(questionId, e.target.value);
            }, 2000);
        }
    });

    // Prevent accidental page leave
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
        logActivity('page_unload');
    });

    // Handle visibility change (user switching tabs/windows)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.warn('User switched away from exam window');
            logActivity('tab_hidden');
        } else {
            logActivity('tab_visible');
        }
    });

    document.addEventListener('copy', function() {
        logActivity('copy');
    });

    document.addEventListener('paste', function() {
        logActivity('paste');
    });

    function logActivity(type, details = '') {
        fetch('<?= base_url('api/log-activity') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                exam_id: <?= $exam['id'] ?>,
                event_type: type,
                details: details
            })
        });
    }
</script>
<?= $this->endSection() ?>