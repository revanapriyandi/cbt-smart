<?php if (session()->get('role') === 'admin'): ?>
    <?= $this->include('components/sidebar/menus/admin_menu') ?>
<?php elseif (session()->get('role') === 'teacher'): ?>
    <?= $this->include('components/sidebar/menus/teacher_menu') ?>
<?php elseif (session()->get('role') === 'student'): ?>
    <?= $this->include('components/sidebar/menus/student_menu') ?>
<?php endif; ?>