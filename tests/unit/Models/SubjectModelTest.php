<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\SubjectModel;
use App\Models\UserModel;

/**
 * @internal
 */
final class SubjectModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $migrate = true;
    protected $seed = 'InitialDataSeeder';

    private SubjectModel $subjectModel;
    private UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subjectModel = new SubjectModel();
        $this->userModel = new UserModel();
    }

    public function testSubjectCreation(): void
    {
        $data = [
            'name' => 'Test Subject',
            'code' => 'TEST001',
            'description' => 'This is a test subject',
            'teacher_id' => null
        ];

        $result = $this->subjectModel->insert($data);
        $this->assertIsNumeric($result);

        $subject = $this->subjectModel->find($result);
        $this->assertSame('Test Subject', $subject['name']);
        $this->assertSame('TEST001', $subject['code']);
    }

    public function testSubjectWithTeacher(): void
    {
        // Create a teacher first
        $teacherData = [
            'username' => 'test_teacher',
            'email' => 'teacher@test.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'full_name' => 'Test Teacher',
            'role' => 'teacher',
            'is_active' => 1
        ];

        $teacherId = $this->userModel->insert($teacherData);

        $subjectData = [
            'name' => 'Subject with Teacher',
            'code' => 'SWT001',
            'description' => 'Subject assigned to teacher',
            'teacher_id' => $teacherId
        ];

        $subjectId = $this->subjectModel->insert($subjectData);
        $subject = $this->subjectModel->find($subjectId);

        $this->assertSame($teacherId, $subject['teacher_id']);
    }

    public function testGetSubjectsWithDetails(): void
    {
        $subjects = $this->subjectModel->getSubjectsWithDetails();
        $this->assertIsArray($subjects);

        if (!empty($subjects)) {
            $firstSubject = $subjects[0];
            $this->assertArrayHasKey('name', $firstSubject);
            $this->assertArrayHasKey('code', $firstSubject);
            $this->assertArrayHasKey('teacher_name', $firstSubject);
        }
    }

    public function testGetSubjectStatistics(): void
    {
        // Get first subject
        $subject = $this->subjectModel->first();

        if ($subject) {
            $stats = $this->subjectModel->getSubjectStatistics($subject['id']);

            $this->assertIsArray($stats);
            $this->assertArrayHasKey('name', $stats);
            $this->assertArrayHasKey('total_exams', $stats);
            $this->assertArrayHasKey('active_exams', $stats);
            $this->assertArrayHasKey('enrolled_students', $stats);
        }
    }

    public function testSubjectValidation(): void
    {
        // Test missing required fields
        $invalidData = [
            'description' => 'Missing name and code'
        ];

        $result = $this->subjectModel->insert($invalidData);
        $this->assertFalse($result);

        $errors = $this->subjectModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testUniqueCodeConstraint(): void
    {
        $data1 = [
            'name' => 'Subject 1',
            'code' => 'UNIQUE001',
            'description' => 'First subject'
        ];

        $data2 = [
            'name' => 'Subject 2',
            'code' => 'UNIQUE001', // Same code
            'description' => 'Second subject'
        ];

        $result1 = $this->subjectModel->insert($data1);
        $this->assertIsNumeric($result1);

        $result2 = $this->subjectModel->insert($data2);
        $this->assertFalse($result2);
    }
}
