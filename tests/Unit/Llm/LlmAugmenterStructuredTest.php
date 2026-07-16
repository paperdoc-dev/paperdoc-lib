<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Llm;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\LlmProviderInterface;
use Paperdoc\Exceptions\LlmException;
use Paperdoc\Llm\LlmAugmenter;

class FakeProvider implements LlmProviderInterface
{
    /** @var list<array{system: string, content: array, options: array}> */
    public array $calls = [];

    /** @param string[] $answers */
    public function __construct(private array $answers) {}

    public function chat(string $systemPrompt, array $content, array $options = []): string
    {
        $this->calls[] = ['system' => $systemPrompt, 'content' => $content, 'options' => $options];

        return array_shift($this->answers) ?? '';
    }
}

class LlmAugmenterStructuredTest extends TestCase
{
    private function augmenter(FakeProvider $provider): LlmAugmenter
    {
        return new LlmAugmenter(['options' => ['temperature' => 0.1]], $provider);
    }

    public function test_enhance_returns_trimmed_answer(): void
    {
        $provider = new FakeProvider(["  Corrected text.  \n"]);

        $this->assertSame('Corrected text.', $this->augmenter($provider)->enhance('Corupted teqt.'));
        $this->assertSame(0.1, $provider->calls[0]['options']['temperature']);
        $this->assertStringContainsString('OCR post-processing', $provider->calls[0]['system']);
    }

    public function test_structure_document_parses_plain_json(): void
    {
        $provider = new FakeProvider([
            '{"title":"Facture","paragraphs":["Ligne 1","Ligne 2"],"tables":[],"confidence":0.9}',
        ]);

        $result = $this->augmenter($provider)->structureDocument('raw ocr');

        $this->assertSame('Facture', $result['title']);
        $this->assertSame(['Ligne 1', 'Ligne 2'], $result['paragraphs']);
        $this->assertSame([], $result['tables']);
        $this->assertSame(0.9, $result['confidence']);
    }

    public function test_structure_document_strips_markdown_fence(): void
    {
        $provider = new FakeProvider([
            "```json\n{\"title\":\"T\",\"paragraphs\":[],\"tables\":[],\"confidence\":1.5}\n```",
        ]);

        $result = $this->augmenter($provider)->structureDocument('x');

        $this->assertSame('T', $result['title']);
        $this->assertSame(1.0, $result['confidence']);
    }

    public function test_structure_document_retries_on_invalid_json(): void
    {
        $provider = new FakeProvider([
            'Sorry, here is prose instead of JSON.',
            '{"title":"Retry win","paragraphs":["p"],"tables":[],"confidence":0.5}',
        ]);

        $result = $this->augmenter($provider)->structureDocument('x');

        $this->assertSame('Retry win', $result['title']);
        $this->assertCount(2, $provider->calls);
    }

    public function test_structure_document_throws_after_max_attempts(): void
    {
        $provider = new FakeProvider(['nope', 'still nope', 'never json']);

        $this->expectException(LlmException::class);

        $this->augmenter($provider)->structureDocument('x');
    }

    public function test_structure_document_sends_image_block(): void
    {
        $image = tempnam(sys_get_temp_dir(), 'pd_img_') . '.png';
        file_put_contents($image, 'fake-png-bytes');

        $provider = new FakeProvider([
            '{"title":"","paragraphs":[],"tables":[],"confidence":0.2}',
        ]);

        $this->augmenter($provider)->structureDocument('', $image);
        @unlink($image);

        $blocks = $provider->calls[0]['content'];
        $this->assertSame('image', $blocks[0]['type']);
        $this->assertSame('image/png', $blocks[0]['mimeType']);
        $this->assertSame(base64_encode('fake-png-bytes'), $blocks[0]['data']);
        $this->assertSame('text', $blocks[1]['type']);
    }
}
