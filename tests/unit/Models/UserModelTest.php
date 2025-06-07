<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UserModel;

/**
 * @internal
 */
final class UserModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $migrate = true;

    private UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
    }

    public function testCreateUser(): void
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'full_name' => 'Test User',
            'role' => 'student',
            'is_active' => 1
        ];

        $userId = $this->userModel->insert($data);
        $this->assertIsNumeric($userId);

        $user = $this->userModel->find($userId);
        $this->assertSame('testuser', $user['username']);
        $this->assertSame('test@example.com', $user['email']);
        $this->assertSame('student', $user['role']);
    }

    public function testPasswordHashing(): void
    {
        $plainPassword = 'mypassword123';
        $data = [
            'username' => 'hashtest',
            'email' => 'hash@test.com',
            'password' => $plainPassword,
            'full_name' => 'Hash Test',
            'role' => 'student'
        ];

        $userId = $this->userModel->insert($data);
        $user = $this->userModel->find($userId);

        // Password should be hashed, not plain text
        $this->assertNotSame($plainPassword, $user['password']);
        $this->assertTrue(password_verify($plainPassword, $user['password']));
    }

    public function testGetTeachers(): void
    {
        // Create some users with different roles
        $users = [
            [
                'username' => 'teacher1',
                'email' => 'teacher1@test.com',
                'password' => 'password',
                'full_name' => 'Teacher One',
                'role' => 'teacher'
            ],
            [
                'username' => 'student1',
                'email' => 'student1@test.com',
                'password' => 'password',
                'full_name' => 'Student One',
                'role' => 'student'
            ],
            [
                'username' => 'teacher2',
                'email' => 'teacher2@test.com',
                'password' => 'password',
                'full_name' => 'Teacher Two',
                'role' => 'teacher'
            ]
        ];

        foreach ($users as $userData) {
            $this->userModel->insert($userData);
        }

        $teachers = $this->userModel->getTeachers();

        $this->assertIsArray($teachers);
        $this->assertCount(2, $teachers);

        foreach ($teachers as $teacher) {
            $this->assertSame('teacher', $teacher['role']);
        }
    }

    public function testUniqueConstraints(): void
    {
        $data1 = [
            'username' => 'unique_user',
            'email' => 'unique@test.com',
            'password' => 'password',
            'full_name' => 'Unique User',
            'role' => 'student'
        ];

        $result1 = $this->userModel->insert($data1);
        $this->assertIsNumeric($result1);

        // Try to create user with same username
        $data2 = [
            'username' => 'unique_user', // Same username
            'email' => 'different@test.com',
            'password' => 'password',
            'full_name' => 'Different User',
            'role' => 'student'
        ];

        $result2 = $this->userModel->insert($data2);
        $this->assertFalse($result2);

        // Try to create user with same email
        $data3 = [
            'username' => 'different_user',
            'email' => 'unique@test.com', // Same email
            'password' => 'password',
            'full_name' => 'Another User',
            'role' => 'student'
        ];

        $result3 = $this->userModel->insert($data3);
        $this->assertFalse($result3);
    }

    public function testValidation(): void
    {
        // Test invalid email
        $invalidData = [
            'username' => 'testuser',
            'email' => 'invalid-email',
            'password' => 'password',
            'full_name' => 'Test User',
            'role' => 'student'
        ];

        $result = $this->userModel->insert($invalidData);
        $this->assertFalse($result);

        $errors = $this->userModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testRoleValidation(): void
    {
        $invalidData = [
            'username' => 'testuser',
            'email' => 'valid@test.com',
            'password' => 'password',
            'full_name' => 'Test User',
            'role' => 'invalid_role' // Invalid role
        ];

        $result = $this->userModel->insert($invalidData);
        $this->assertFalse($result);
    }
}
