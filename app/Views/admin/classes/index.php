<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Classes<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Manage Classes</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Comprehensive class management with student enrollment</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3"> <a href="<?= base_url('admin/classes/import') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import CSV
            </a>
            <button onclick="exportClasses()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
            <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Class
            </button>
        </div>
    </div>

    <!-- Include Statistics Component -->
    <?= $this->include('admin/classes/components/statistics') ?>

    <!-- Include Filters Component -->
    <?= $this->include('admin/classes/components/filters') ?>

    <!-- Include Classes Table Component -->
    <?= $this->include('admin/classes/components/table') ?>
</div>

<!-- Include Modals Component -->
<?= $this->include('admin/classes/components/modals') ?>

<!-- Include Scripts Component -->
<?= $this->include('admin/classes/components/scripts') ?>
<?= $this->endSection() ?>