<?php

// Simple script to insert test data manually using PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cbt_smart', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Connected to database successfully\n";

    // Check if users exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    if ($userCount > 0) {
        echo "Users already exist. Count: $userCount\n";
        // Show existing users
        $stmt = $pdo->query("SELECT username, role FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            echo "- {$user['username']} ({$user['role']})\n";
        }
    } else {
        echo "Inserting test users...\n";

        // Insert admin
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute(['admin', 'admin@cbt.com', password_hash('admin123', PASSWORD_DEFAULT), 'Administrator', 'admin']);

        // Insert teacher
        $stmt->execute(['teacher1', 'teacher1@cbt.com', password_hash('teacher123', PASSWORD_DEFAULT), 'Teacher One', 'teacher']);

        // Insert student
        $stmt->execute(['student1', 'student1@cbt.com', password_hash('student123', PASSWORD_DEFAULT), 'Student One', 'student']);

        echo "Users inserted successfully\n";
    }

    // Check subjects
    $stmt = $pdo->query("SELECT COUNT(*) FROM subjects");
    $subjectCount = $stmt->fetchColumn();

    if ($subjectCount == 0) {
        echo "Inserting test subjects...\n";

        $stmt = $pdo->prepare("INSERT INTO subjects (name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute(['Matematika', 'Mata pelajaran Matematika']);
        $stmt->execute(['Bahasa Indonesia', 'Mata pelajaran Bahasa Indonesia']);

        echo "Subjects inserted successfully\n";
    } else {
        echo "Subjects already exist. Count: $subjectCount\n";
    }

    echo "\nLogin credentials:\n";
    echo "Admin: admin / admin123\n";
    echo "Teacher: teacher1 / teacher123\n";
    echo "Student: student1 / student123\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
