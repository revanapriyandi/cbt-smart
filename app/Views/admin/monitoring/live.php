<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .pulse-ring {
        position: relative;
    }

    .pulse-ring::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        border: 2px solid currentColor;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        animation: pulse-ring 1.5s ease-out infinite;
        opacity: 0.7;
    }

    @keyframes pulse-ring {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 1;
        }

        80%,
        100% {
            transform: translate(-50%, -50%) scale(1.8);
            opacity: 0;
        }
    }

    .progress-bar-animated {
        background-size: 1rem 1rem;
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% {
            background-position-x: 1rem;
        }
    }

    .status-indicator {
        position: relative;
        display: inline-block;
    }

    .status-indicator.online::after {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background: #10b981;
        border: 2px solid white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .modal-backdrop {
        backdrop-filter: blur(4px);
    }

    .notification-enter {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease;
    }

    .notification-exit {
        transform: translateX(0);
        opacity: 1;
    }

    .notification-exit-active {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .health-meter {
        background: linear-gradient(90deg, #10b981 0%, #f59e0b 50%, #ef4444 100%);
        height: 4px;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
    }

    .health-meter::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .floating-action {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 40;
    }

    .floating-action button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .floating-action button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    .status-badge {
        animation: status-pulse 2s infinite;
    }

    @keyframes status-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .activity-timeline {
        position: relative;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6, #ec4899);
    }

    .activity-item {
        position: relative;
        z-index: 1;
    }

    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .lg\\:col-span-2 {
            grid-column: span 1;
        }

        .lg\\:col-span-1 {
            grid-column: span 1;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6 relative" x-data="liveMonitoring()">

    <?= $this->include('admin/monitoring/components/loading') ?>

    <?= $this->include('admin/monitoring/components/header') ?>

    <?= $this->include('admin/monitoring/components/statistics') ?>

    <?= $this->include('admin/monitoring/components/system_health') ?>

    <?= $this->include('admin/monitoring/components/active_sessions') ?>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <?= $this->include('admin/monitoring/components/alerts') ?>
        <?= $this->include('admin/monitoring/components/activities') ?>
    </div>

    <?= $this->include('admin/monitoring/components/floating_action') ?>
</div>

<?= $this->include('admin/monitoring/components/modals') ?>

<?= $this->include('admin/monitoring/components/scripts') ?>

<?= $this->endSection() ?>