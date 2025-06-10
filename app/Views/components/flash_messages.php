<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg"
        x-data="{ show: true }" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-green-800 text-sm font-medium">
                    <?= session()->getFlashdata('success') ?>
                </p>
            </div>
            <div class="ml-auto">
                <button @click="show = false"
                    class="text-green-500 hover:text-green-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg"
        x-data="{ show: true }" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-red-800 text-sm font-medium">
                    <?= session()->getFlashdata('error') ?>
                </p>
            </div>
            <div class="ml-auto">
                <button @click="show = false"
                    class="text-red-500 hover:text-red-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('info')): ?>
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg"
        x-data="{ show: true }" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-blue-800 text-sm font-medium">
                    <?= session()->getFlashdata('info') ?>
                </p>
            </div>
            <div class="ml-auto">
                <button @click="show = false"
                    class="text-blue-500 hover:text-blue-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
        x-data="{ show: true }" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-yellow-800 text-sm font-medium">
                    <?= session()->getFlashdata('warning') ?>
                </p>
            </div>
            <div class="ml-auto">
                <button @click="show = false"
                    class="text-yellow-500 hover:text-yellow-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>