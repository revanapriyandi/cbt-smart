<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Manage Users</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Comprehensive user management with role separation</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="openImportModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import CSV
            </button>            <button onclick="exportUsers()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
            <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </button>
        </div>
    </div>

    <!-- Include Statistics Component -->
    <?= $this->include('admin/users/components/statistics') ?>

    <!-- Include Filters Component -->
    <?= $this->include('admin/users/components/filters') ?>

    <!-- Include Users Table Component -->
    <?= $this->include('admin/users/components/table') ?>
</div>

<!-- Include Modals Component -->
<?= $this->include('admin/users/components/modals') ?>

<!-- Include Scripts Component -->
<?= $this->include('admin/users/components/scripts') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    /* Custom DataTables styling */
    .dataTables_wrapper {
        font-family: inherit;
    }

    .dataTables_processing {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        width: auto !important;
        margin: 0 !important;
        transform: translate(-50%, -50%) !important;
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        padding: 1rem 1.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        color: #374151 !important;
        font-weight: 500 !important;
    }

    /* Hide default DataTables elements */
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }

    /* Table styling */
    #users-table tbody tr:hover {
        background-color: #f9fafb !important;
    }

    #users-table tbody tr.selected {
        background-color: #eff6ff !important;
    }

    /* Responsive table adjustments */
    @media (max-width: 768px) {

        #users-table thead th:nth-child(4),
        #users-table tbody td:nth-child(4),
        #users-table thead th:nth-child(5),
        #users-table tbody td:nth-child(5) {
            display: none;
        }
    }

    @media (max-width: 640px) {

        #users-table thead th:nth-child(3),
        #users-table tbody td:nth-child(3) {
            display: none;
        }
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Animation for bulk actions */
    #bulk-actions {
        transition: all 0.3s ease-in-out;
    }

    /* Enhanced loading overlay */
    #table-loading {
        backdrop-filter: blur(2px);
    }

    /* Improved action buttons */
    .action-btn {
        transition: all 0.2s ease-in-out;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Status badges animations */
    .status-badge {
        transition: all 0.2s ease-in-out;
    }

    .status-badge:hover {
        transform: scale(1.05);
    }

    /* Table row selection effect */
    .user-checkbox:checked+* {
        background-color: #eff6ff;
    }

    /* Custom pagination styling */
    #dt-pagination button:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    #dt-pagination button:not(:disabled):hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Smooth transitions */
    * {
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }
</style>
<?= $this->endSection() ?>