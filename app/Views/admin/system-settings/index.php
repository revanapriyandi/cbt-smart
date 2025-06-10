<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                System Settings
            </h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Configure system settings and preferences</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="systemInfoBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                System Info
            </button>
            <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="clearCacheBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear Cache
            </button>
            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="resetDefaultsBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Defaults
            </button>
        </div>
    </div>    <!-- Settings Tabs -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md">
                    <option value="#general">General Settings</option>
                    <option value="#email">Email Settings</option>
                    <option value="#exam">Exam Settings</option>
                    <option value="#notification">Notifications</option>
                    <option value="#maintenance">Maintenance</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button type="button" class="tab-button active border-purple-500 text-purple-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="general">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        General Settings
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="email">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Settings
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="exam">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exam Settings
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="notification">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Notifications
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-tab="maintenance">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Maintenance
                    </button>
                </nav>
            </div>
        </div>        <div class="p-6">
            <div class="tab-content">
                <!-- General Settings Tab -->
                <div class="tab-pane active" id="general" role="tabpanel">
                    <form id="generalSettingsForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input type="text" id="site_name" name="site_name" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="CBT Smart System">
                            </div>
                            <div>
                                <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                                <input type="text" id="site_description" name="site_description" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="Computer Based Testing System">
                            </div>
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                                <input type="email" id="admin_email" name="admin_email" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="admin@example.com">
                            </div>
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                <select id="timezone" name="timezone" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>
                            <div>
                                <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                                <input type="file" id="site_logo" name="site_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-sm text-gray-500">Upload PNG, JPG, or GIF. Max size: 2MB</p>
                            </div>
                            <div>
                                <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                                <select id="date_format" name="date_format" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                    <option value="Y-m-d">YYYY-MM-DD</option>
                                    <option value="d/m/Y">DD/MM/YYYY</option>
                                    <option value="m/d/Y">MM/DD/YYYY</option>
                                    <option value="d-m-Y">DD-MM-YYYY</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>                <!-- Email Settings Tab -->
                <div class="tab-pane hidden" id="email" role="tabpanel">
                    <form id="emailSettingsForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                <input type="text" id="smtp_host" name="smtp_host" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="smtp.gmail.com">
                            </div>
                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                <input type="number" id="smtp_port" name="smtp_port" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="587">
                            </div>
                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                                <input type="text" id="smtp_username" name="smtp_username" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="your-email@gmail.com">
                            </div>
                            <div>
                                <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                                <input type="password" id="smtp_password" name="smtp_password" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="Your app password">
                            </div>
                            <div>
                                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-2">SMTP Encryption</label>
                                <select id="smtp_encryption" name="smtp_encryption" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
                                </select>
                            </div>
                            <div>
                                <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                                <input type="email" id="from_email" name="from_email" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="noreply@example.com">
                            </div>
                        </div>
                        <div class="mt-6">
                            <div>
                                <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                                <input type="text" id="from_name" name="from_name" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="CBT Smart System">
                            </div>
                        </div>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save Email Settings
                            </button>
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center" id="testEmailBtn">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Test Email
                            </button>
                        </div>
                    </form>
                </div>                <!-- Exam Settings Tab -->
                <div class="tab-pane hidden" id="exam" role="tabpanel">
                    <form id="examSettingsForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="default_exam_duration" class="block text-sm font-medium text-gray-700 mb-2">Default Exam Duration (minutes)</label>
                                <input type="number" id="default_exam_duration" name="default_exam_duration" value="120" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="auto_submit_buffer" class="block text-sm font-medium text-gray-700 mb-2">Auto Submit Buffer (minutes)</label>
                                <input type="number" id="auto_submit_buffer" name="auto_submit_buffer" value="5" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <p class="mt-1 text-sm text-gray-500">Extra time before auto-submit</p>
                            </div>
                            <div>
                                <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-2">Default Passing Score (%)</label>
                                <input type="number" id="passing_score" name="passing_score" min="0" max="100" value="60" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Maximum Attempts</label>
                                <input type="number" id="max_attempts" name="max_attempts" min="1" value="1" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="allow_review" name="allow_review" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="allow_review" class="ml-2 block text-sm text-gray-900">Allow Question Review</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="shuffle_questions" name="shuffle_questions" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="shuffle_questions" class="ml-2 block text-sm text-gray-900">Shuffle Questions by Default</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save Exam Settings
                            </button>
                        </div>
                    </form>
                </div>                <!-- Notification Settings Tab -->
                <div class="tab-pane hidden" id="notification" role="tabpanel">
                    <form id="notificationSettingsForm">
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="email_notifications" name="email_notifications" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="email_notifications" class="ml-2 block text-sm text-gray-900">Enable Email Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="exam_start_notification" name="exam_start_notification" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="exam_start_notification" class="ml-2 block text-sm text-gray-900">Notify on Exam Start</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="exam_end_notification" name="exam_end_notification" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="exam_end_notification" class="ml-2 block text-sm text-gray-900">Notify on Exam Completion</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="user_registration_notification" name="user_registration_notification" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="user_registration_notification" class="ml-2 block text-sm text-gray-900">Notify on User Registration</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>                <!-- Maintenance Settings Tab -->
                <div class="tab-pane hidden" id="maintenance" role="tabpanel">
                    <form id="maintenanceSettingsForm">
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="maintenance_mode" class="ml-2 block text-sm text-gray-900">Enable Maintenance Mode</label>
                            </div>
                            <div>
                                <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
                                <textarea id="maintenance_message" name="maintenance_message" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="System is under maintenance. Please try again later."></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="backup_retention_days" class="block text-sm font-medium text-gray-700 mb-2">Backup Retention (days)</label>
                                    <input type="number" id="backup_retention_days" name="backup_retention_days" value="30" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                </div>
                                <div>
                                    <label for="log_retention_days" class="block text-sm font-medium text-gray-700 mb-2">Log Retention (days)</label>
                                    <input type="number" id="log_retention_days" name="log_retention_days" value="90" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save Maintenance Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div id="systemInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">System Information</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeSystemInfoModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="systemInfoContent">
            <!-- Content will be loaded via JavaScript -->
        </div>
        <div class="flex justify-end mt-6">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md" onclick="closeSystemInfoModal()">Close</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Tab functionality
        $('.tab-button').click(function() {
            const targetTab = $(this).data('tab');
            
            // Remove active classes from all tabs
            $('.tab-button').removeClass('active border-purple-500 text-purple-600').addClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
            $('.tab-pane').removeClass('active').addClass('hidden');
            
            // Add active classes to clicked tab
            $(this).removeClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300').addClass('active border-purple-500 text-purple-600');
            $('#' + targetTab).removeClass('hidden').addClass('active');
        });

        // Mobile tab select
        $('#tabs').change(function() {
            const targetTab = $(this).val().substring(1); // Remove # from value
            $('.tab-pane').removeClass('active').addClass('hidden');
            $('#' + targetTab).removeClass('hidden').addClass('active');
        });

        // Show/hide modals
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal functions
        window.closeSystemInfoModal = function() {
            hideModal('systemInfoModal');
        };

        // Load current settings
        loadSettings();

        // General settings form
        $('#generalSettingsForm').submit(function(e) {
            e.preventDefault();
            saveSettings('general', $(this).serialize());
        });

        // Email settings form
        $('#emailSettingsForm').submit(function(e) {
            e.preventDefault();
            saveSettings('email', $(this).serialize());
        });

        // Exam settings form
        $('#examSettingsForm').submit(function(e) {
            e.preventDefault();
            saveSettings('exam', $(this).serialize());
        });

        // Notification settings form
        $('#notificationSettingsForm').submit(function(e) {
            e.preventDefault();
            saveSettings('notification', $(this).serialize());
        });

        // Maintenance settings form
        $('#maintenanceSettingsForm').submit(function(e) {
            e.preventDefault();
            saveSettings('maintenance', $(this).serialize());
        });        // Test email button
        $('#testEmailBtn').click(function() {
            $.ajax({
                url: '<?= base_url('admin/system-settings/test-email') ?>',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Failed to test email configuration', 'error');
                }
            });
        });

        // System info button
        $('#systemInfoBtn').click(function() {
            $.ajax({
                url: '<?= base_url('admin/system-settings/info') ?>',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let info = response.info;
                        let infoHtml = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h6 class="text-lg font-medium text-gray-900 mb-4">Server Information</h6>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <table class="min-w-full">
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">PHP Version</td><td class="py-2 text-sm text-gray-900">${info.php_version}</td></tr>
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">CodeIgniter Version</td><td class="py-2 text-sm text-gray-900">${info.ci_version}</td></tr>
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">Database Version</td><td class="py-2 text-sm text-gray-900">${info.db_version}</td></tr>
                                        <tr><td class="py-2 text-sm font-medium text-gray-600">Server Software</td><td class="py-2 text-sm text-gray-900">${info.server_software}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-lg font-medium text-gray-900 mb-4">System Resources</h6>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <table class="min-w-full">
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">Memory Limit</td><td class="py-2 text-sm text-gray-900">${info.memory_limit}</td></tr>
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">Upload Max Size</td><td class="py-2 text-sm text-gray-900">${info.upload_max_filesize}</td></tr>
                                        <tr class="border-b border-gray-200"><td class="py-2 text-sm font-medium text-gray-600">Disk Space</td><td class="py-2 text-sm text-gray-900">${info.disk_free_space}</td></tr>
                                        <tr><td class="py-2 text-sm font-medium text-gray-600">Timezone</td><td class="py-2 text-sm text-gray-900">${info.timezone}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                        $('#systemInfoContent').html(infoHtml);
                        showModal('systemInfoModal');
                    }
                }
            });
        });

        // Clear cache button
        $('#clearCacheBtn').click(function() {
            if (confirm('Clear System Cache? This will clear all cached data.')) {
                $.ajax({
                    url: '<?= base_url('admin/system-settings/clear-cache') ?>',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                        } else {
                            showNotification(response.message, 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to clear cache', 'error');
                    }
                });
            }
        });

        // Reset defaults button
        $('#resetDefaultsBtn').click(function() {
            if (confirm('Reset to Default Settings? This will reset all settings to their default values. This action cannot be undone.')) {
                $.ajax({
                    url: '<?= base_url('admin/system-settings/reset-defaults') ?>',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                            loadSettings(); // Reload settings
                        } else {
                            showNotification(response.message, 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to reset settings', 'error');
                    }
                });
            }
        });        function loadSettings() {
            // This would typically load current settings from the server
            // For now, we'll assume they're loaded or set default values
        }

        function saveSettings(category, formData) {
            $.ajax({
                url: '<?= base_url('admin/system-settings/update-') ?>' + category,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Failed to save settings', 'error');
                }
            });
        }

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = $(`
                <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 notification">
                    ${message}
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 3000);
        }
    });
</script>
<?= $this->endSection() ?>