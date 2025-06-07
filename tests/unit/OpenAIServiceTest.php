<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Libraries\OpenAIService;

final class OpenAIServiceTest extends CIUnitTestCase
{
    public function testParseGradingResponseExtractsScoreAndFeedback(): void
    {
        $service = new OpenAIService();
        $ref = new ReflectionClass(OpenAIService::class);
        $method = $ref->getMethod('parseGradingResponse');
        $method->setAccessible(true);

        $response = "SKOR: 8.5\nFEEDBACK: Jawaban sudah baik";
        $result = $method->invoke($service, $response, 10);

        $this->assertTrue($result['success']);
        $this->assertSame(8.5, $result['score']);
        $this->assertSame('Jawaban sudah baik', $result['feedback']);
    }

    public function testParseGradingResponseCapsScoreAtMax(): void
    {
        $service = new OpenAIService();
        $ref = new ReflectionClass(OpenAIService::class);
        $method = $ref->getMethod('parseGradingResponse');
        $method->setAccessible(true);

        $response = "SKOR: 15\nFEEDBACK: Luar biasa";
        $result = $method->invoke($service, $response, 10);

        $this->assertSame(10.0, $result['score']);
    }

    public function testParseGeneratedQuestionsParsesAllQuestions(): void
    {
        $service = new OpenAIService();
        $ref = new ReflectionClass(OpenAIService::class);
        $method = $ref->getMethod('parseGeneratedQuestions');
        $method->setAccessible(true);

        $text = "SOAL 1: Apa itu AI? SOAL 2: Bagaimana cara kerja machine learning? SOAL 3: Contoh penerapan AI?";
        $result = $method->invoke($service, $text);

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['questions']);
        $this->assertSame(1, $result['questions'][0]['number']);
        $this->assertSame('Apa itu AI?', $result['questions'][0]['text']);
    }
}
