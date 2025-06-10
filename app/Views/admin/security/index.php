<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Security Settings
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Manage system security policies and monitoring</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="exportSecurityReport('pdf')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Report
                        </button>
                        <button onclick="refreshSecurityData()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Failed Logins (24h)</dt>
                                <dd class="text-lg font-medium text-gray-900" id="failed-logins-count">
                                    <?= $security_dashboard['failed_logins_24h'] ?? 0 ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Successful Logins (24h)</dt>
                                <dd class="text-lg font-medium text-gray-900" id="successful-logins-count">
                                    <?= $security_dashboard['successful_logins_24h'] ?? 0 ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Sessions</dt>
                                <dd class="text-lg font-medium text-gray-900" id="active-sessions-count">
                                    <?= $security_dashboard['active_sessions'] ?? 0 ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Blocked IPs</dt>
                                <dd class="text-lg font-medium text-gray-900" id="blocked-ips-count">
                                    <?= $security_dashboard['blocked_ips_count'] ?? 0 ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="switchTab('general')" id="tab-general" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        General Settings
                    </button>
                    <button onclick="switchTab('password')" id="tab-password" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Password Policies
                    </button>
                    <button onclick="switchTab('session')" id="tab-session" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Session Settings
                    </button>
                    <button onclick="switchTab('monitoring')" id="tab-monitoring" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Monitoring
                    </button>
                </nav>
            </div>

            <!-- General Settings Tab -->
            <div id="content-general" class="tab-content p-6">
                <form id="general-settings-form" onsubmit="updateGeneralSettings(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Authentication Settings</h3>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-base font-medium text-gray-900">Two-Factor Authentication</label>
                                    <p class="text-sm text-gray-500">Require 2FA for all admin accounts</p>
                                </div>
                                <input type="checkbox" name="two_factor_required" value="1" 
                                       <?= ($settings['general']['two_factor_required'] ?? '0') == '1' ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-base font-medium text-gray-900">Force Password Reset</label>
                                    <p class="text-sm text-gray-500">Require password change on first login</p>
                                </div>
                                <input type="checkbox" name="password_reset_required" value="1"
                                       <?= ($settings['general']['password_reset_required'] ?? '0') == '1' ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-base font-medium text-gray-900">Maintenance Mode</label>
                                    <p class="text-sm text-gray-500">Enable system maintenance mode</p>
                                </div>
                                <input type="checkbox" name="maintenance_mode" value="1"
                                       <?= ($settings['general']['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Security Policies</h3>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-base font-medium text-gray-900">Account Lockout</label>
                                    <p class="text-sm text-gray-500">Lock accounts after failed attempts</p>
                                </div>
                                <input type="checkbox" name="account_lockout_enabled" value="1"
                                       <?= ($settings['general']['account_lockout_enabled'] ?? '1') == '1' ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Max Login Attempts</label>
                                <input type="number" name="max_login_attempts" min="1" max="10"
                                       value="<?= $settings['general']['max_login_attempts'] ?? '5' ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lockout Duration (minutes)</label>
                                <input type="number" name="lockout_duration" min="5" max="1440"
                                       value="<?= intval(($settings['general']['lockout_duration'] ?? '900') / 60) ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-base font-medium text-gray-900">IP Whitelist</label>
                                    <p class="text-sm text-gray-500">Restrict access to whitelisted IPs</p>
                                </div>
                                <input type="checkbox" name="ip_whitelist_enabled" value="1"
                                       <?= ($settings['general']['ip_whitelist_enabled'] ?? '0') == '1' ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Policies Tab -->
            <div id="content-password" class="tab-content p-6 hidden">
                <form id="password-policies-form" onsubmit="updatePasswordPolicies(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Password Requirements</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Minimum Length</label>
                                <input type="number" name="min_length" min="6" max="50"
                                       value="<?= $password_policies['min_length'] ?? '8' ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="require_uppercase" value="1"
                                           <?= ($password_policies['require_uppercase'] ?? '1') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require uppercase letters</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="require_lowercase" value="1"
                                           <?= ($password_policies['require_lowercase'] ?? '1') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require lowercase letters</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="require_numbers" value="1"
                                           <?= ($password_policies['require_numbers'] ?? '1') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require numbers</label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="require_symbols" value="1"
                                           <?= ($password_policies['require_symbols'] ?? '0') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Require special symbols</label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Password Management</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password History</label>
                                <input type="number" name="password_history" min="0" max="20"
                                       value="<?= $password_policies['password_history'] ?? '5' ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <p class="mt-1 text-sm text-gray-500">Number of previous passwords to remember</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password Expiry (days)</label>
                                <input type="number" name="password_expiry_days" min="30" max="365"
                                       value="<?= $password_policies['password_expiry_days'] ?? '90' ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <p class="mt-1 text-sm text-gray-500">Password expires after this many days</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Policies
                        </button>
                    </div>
                </form>
            </div>

            <!-- Session Settings Tab -->
            <div id="content-session" class="tab-content p-6 hidden">
                <form id="session-settings-form" onsubmit="updateSessionSettings(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Session Timeouts</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" min="15" max="480"
                                       value="<?= intval(($session_settings['session_timeout'] ?? '7200') / 60) ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Idle Timeout (minutes)</label>
                                <input type="number" name="idle_timeout" min="5" max="120"
                                       value="<?= intval(($session_settings['idle_timeout'] ?? '1800') / 60) ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Remember Me Duration (days)</label>
                                <input type="number" name="remember_me_duration" min="1" max="90"
                                       value="<?= intval(($session_settings['remember_me_duration'] ?? '2592000') / 86400) ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Session Management</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Max Concurrent Sessions</label>
                                <input type="number" name="concurrent_sessions" min="1" max="10"
                                       value="<?= $session_settings['concurrent_sessions'] ?? '3' ?>"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-base font-medium text-gray-900">Secure Cookies</label>
                                        <p class="text-sm text-gray-500">Use secure HTTPS-only cookies</p>
                                    </div>
                                    <input type="checkbox" name="secure_cookies" value="1"
                                           <?= ($session_settings['secure_cookies'] ?? '1') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-base font-medium text-gray-900">Force Logout on Password Change</label>
                                        <p class="text-sm text-gray-500">Logout all sessions when password changes</p>
                                    </div>
                                    <input type="checkbox" name="force_logout_on_password_change" value="1"
                                           <?= ($session_settings['force_logout_on_password_change'] ?? '1') == '1' ? 'checked' : '' ?>
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Monitoring Tab -->
            <div id="content-monitoring" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Security Events -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Security Events</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php if (!empty($security_logs)): ?>
                                <?php foreach ($security_logs as $log): ?>
                                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex-shrink-0">
                                            <?php if ($log['event_type'] === 'login_failed'): ?>
                                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                </div>
                                            <?php elseif ($log['event_type'] === 'login_success'): ?>
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-medium text-gray-900"><?= esc($log['description']) ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?= esc($log['username'] ?? 'Unknown') ?> • <?= esc($log['ip_address']) ?> • 
                                                <?= date('M j, H:i', strtotime($log['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No recent security events</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Active Sessions -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Active Sessions</h3>
                            <button onclick="refreshActiveSessions()" class="text-indigo-600 hover:text-indigo-900 text-sm">Refresh</button>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto" id="active-sessions-list">
                            <?php if (!empty($active_sessions)): ?>
                                <?php foreach ($active_sessions as $session): ?>
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900"><?= esc($session['ip_address']) ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?= date('M j, H:i', $session['timestamp']) ?>
                                                <?php if (strlen($session['user_agent']) > 50): ?>
                                                    • <?= esc(substr($session['user_agent'], 0, 50)) ?>...
                                                <?php else: ?>
                                                    • <?= esc($session['user_agent']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <button onclick="terminateSession('<?= esc($session['id']) ?>')" 
                                                class="text-red-600 hover:text-red-900 text-sm">
                                            Terminate
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No active sessions</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Failed Login Attempts -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Failed Login Attempts (24h)</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php if (!empty($failed_attempts)): ?>
                                <?php foreach ($failed_attempts as $attempt): ?>
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900"><?= esc($attempt['ip_address']) ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?= $attempt['attempts'] ?> attempts • Last: <?= date('M j, H:i', strtotime($attempt['last_attempt'])) ?>
                                            </p>
                                        </div>
                                        <button onclick="blockIP('<?= esc($attempt['ip_address']) ?>')" 
                                                class="text-red-600 hover:text-red-900 text-sm">
                                            Block IP
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No failed attempts</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Blocked IPs -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Blocked IP Addresses</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto" id="blocked-ips-list">
                            <?php if (!empty($blocked_ips)): ?>
                                <?php foreach ($blocked_ips as $blocked): ?>
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= str_replace(['blocked_ip_', '_'], ['', '.'], $blocked['setting_key']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?= esc($blocked['description'] ?? '') ?></p>
                                        </div>
                                        <button onclick="unblockIP('<?= str_replace(['blocked_ip_', '_'], ['', '.'], $blocked['setting_key']) ?>')" 
                                                class="text-green-600 hover:text-green-900 text-sm">
                                            Unblock
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No blocked IPs</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Processing...</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="loading-message">Please wait while we process your request.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching functionality
function switchTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('[id^="tab-"]').forEach(tabButton => {
        tabButton.classList.remove('border-indigo-500', 'text-indigo-600');
        tabButton.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    activeTab.classList.add('border-indigo-500', 'text-indigo-600');
}

// Show/hide loading modal
function showLoading(message = 'Processing...') {
    document.getElementById('loading-message').textContent = message;
    document.getElementById('loading-modal').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loading-modal').classList.add('hidden');
}

// Show notification
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md border-l-4 ${bgColor} shadow-lg z-50`;
    notification.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                    '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Update general settings
async function updateGeneralSettings(event) {
    event.preventDefault();
    showLoading('Updating security settings...');
    
    const form = document.getElementById('general-settings-form');
    const formData = new FormData(form);
    
    // Convert checkboxes to proper values
    const checkboxes = ['two_factor_required', 'password_reset_required', 'account_lockout_enabled', 'ip_whitelist_enabled', 'maintenance_mode'];
    checkboxes.forEach(name => {
        if (!formData.has(name)) {
            formData.append(name, '0');
        }
    });
    
    // Convert lockout_duration from minutes to seconds
    const lockoutDuration = formData.get('lockout_duration');
    formData.set('lockout_duration', parseInt(lockoutDuration) * 60);
    
    try {
        const response = await fetch('/admin/security/update-general-settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification('Security settings updated successfully');
        } else {
            showNotification(result.message || 'Failed to update settings', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Update password policies
async function updatePasswordPolicies(event) {
    event.preventDefault();
    showLoading('Updating password policies...');
    
    const form = document.getElementById('password-policies-form');
    const formData = new FormData(form);
    
    // Convert checkboxes to proper values
    const checkboxes = ['require_uppercase', 'require_lowercase', 'require_numbers', 'require_symbols'];
    checkboxes.forEach(name => {
        if (!formData.has(name)) {
            formData.append(name, '0');
        }
    });
    
    try {
        const response = await fetch('/admin/security/update-password-policies', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification('Password policies updated successfully');
        } else {
            showNotification(result.message || 'Failed to update policies', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Update session settings
async function updateSessionSettings(event) {
    event.preventDefault();
    showLoading('Updating session settings...');
    
    const form = document.getElementById('session-settings-form');
    const formData = new FormData(form);
    
    // Convert checkboxes to proper values
    const checkboxes = ['secure_cookies', 'force_logout_on_password_change'];
    checkboxes.forEach(name => {
        if (!formData.has(name)) {
            formData.append(name, '0');
        }
    });
    
    // Convert time values to seconds
    const sessionTimeout = formData.get('session_timeout');
    const idleTimeout = formData.get('idle_timeout');
    const rememberDuration = formData.get('remember_me_duration');
    
    formData.set('session_timeout', parseInt(sessionTimeout) * 60);
    formData.set('idle_timeout', parseInt(idleTimeout) * 60);
    formData.set('remember_me_duration', parseInt(rememberDuration) * 86400);
    
    try {
        const response = await fetch('/admin/security/update-session-settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification('Session settings updated successfully');
        } else {
            showNotification(result.message || 'Failed to update settings', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Block IP address
async function blockIP(ipAddress) {
    if (!confirm(`Are you sure you want to block IP address ${ipAddress}?`)) {
        return;
    }
    
    showLoading('Blocking IP address...');
    
    try {
        const response = await fetch('/admin/security/manage-ip-whitelist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                action: 'block',
                ip_address: ipAddress,
                reason: 'Multiple failed login attempts'
            })
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification(`IP address ${ipAddress} has been blocked`);
            refreshSecurityData();
        } else {
            showNotification(result.message || 'Failed to block IP', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Unblock IP address
async function unblockIP(ipAddress) {
    if (!confirm(`Are you sure you want to unblock IP address ${ipAddress}?`)) {
        return;
    }
    
    showLoading('Unblocking IP address...');
    
    try {
        const response = await fetch('/admin/security/unblock-ip', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                ip_address: ipAddress
            })
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification(`IP address ${ipAddress} has been unblocked`);
            refreshSecurityData();
        } else {
            showNotification(result.message || 'Failed to unblock IP', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Terminate session
async function terminateSession(sessionId) {
    if (!confirm('Are you sure you want to terminate this session?')) {
        return;
    }
    
    showLoading('Terminating session...');
    
    try {
        const response = await fetch('/admin/security/terminate-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                session_id: sessionId
            })
        });
        
        const result = await response.json();
        hideLoading();
        
        if (result.success) {
            showNotification('Session terminated successfully');
            refreshActiveSessions();
        } else {
            showNotification(result.message || 'Failed to terminate session', 'error');
        }
    } catch (error) {
        hideLoading();
        showNotification('Network error occurred', 'error');
    }
}

// Refresh active sessions
async function refreshActiveSessions() {
    try {
        const response = await fetch('/admin/security/get-security-dashboard');
        const result = await response.json();
        
        if (result.success && result.data.active_sessions) {
            // Update active sessions count
            document.getElementById('active-sessions-count').textContent = result.data.active_sessions.length;
            
            // Update sessions list - this would need to be implemented based on the returned data structure
            // For now, just refresh the page to get updated data
            location.reload();
        }
    } catch (error) {
        console.error('Failed to refresh active sessions:', error);
    }
}

// Refresh security data
function refreshSecurityData() {
    location.reload();
}

// Export security report
function exportSecurityReport(format) {
    const dateFrom = prompt('Enter start date (YYYY-MM-DD):', new Date(Date.now() - 7*24*60*60*1000).toISOString().split('T')[0]);
    if (!dateFrom) return;
    
    const dateTo = prompt('Enter end date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
    if (!dateTo) return;
    
    showLoading('Generating security report...');
    
    const url = `/admin/security/export-security-report?format=${format}&date_from=${dateFrom}&date_to=${dateTo}`;
    
    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = url;
    link.download = `security_report_${dateFrom}_to_${dateTo}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    hideLoading();
    showNotification(`Security report exported successfully (${format.toUpperCase()})`);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set initial tab
    switchTab('general');
});
</script>

<?= $this->endSection() ?>
