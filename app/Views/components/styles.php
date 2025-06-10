<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        50: '#f0f4ff',
                        100: '#e0e9ff',
                        500: '#667eea',
                        600: '#5a6fd8',
                        700: '#4c5bc4',
                        900: '#2d3748'
                    },
                    secondary: {
                        500: '#764ba2',
                        600: '#6a4190'
                    }
                },
                animation: {
                    'slide-in': 'slideIn 0.3s ease-out',
                    'fade-in': 'fadeIn 0.3s ease-out'
                }
            }
        }
    }
</script>

<style>
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
        }

        to {
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Smooth transitions for sidebar */
    .sidebar-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Responsive spacing adjustments */
    @media (min-width: 1024px) and (max-width: 1440px) {
        .main-content {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (min-width: 1024px) and (max-width: 1280px) {
        .main-content {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }

    /* Hover effects */
    .nav-item-hover {
        transition: all 0.2s ease-in-out;
    }

    .nav-item-hover:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(2px);
    }

    .nav-item-active {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Tooltip for collapsed sidebar */
    .tooltip {
        position: relative;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        text-align: center;
        border-radius: 6px;
        padding: 8px 12px;
        position: absolute;
        z-index: 1000;
        left: 120%;
        top: 50%;
        transform: translateY(-50%);
        white-space: nowrap;
        font-size: 14px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    /* Animation for submenu */
    .submenu-enter {
        animation: submenuSlide 0.3s ease-out;
    }

    @keyframes submenuSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

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
<style>
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .progress-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        border-radius: 0 0 0.5rem 0.5rem;
        transition: width 1s ease-in-out;
    }

    /* Enhanced DataTables styling */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
        transition: all 0.15s ease-in-out;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Custom checkbox styling */
    .class-checkbox:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Enhanced modal backdrop */
    #classModal {
        backdrop-filter: blur(4px);
    }

    /* Loading overlay */
    #table-loading {
        backdrop-filter: blur(2px);
    }

    /* Responsive table enhancements */
    @media (max-width: 768px) {
        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>