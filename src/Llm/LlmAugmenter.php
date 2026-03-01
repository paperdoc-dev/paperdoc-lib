<?php

declare(strict_types=1);

namespace Paperdoc\Llm;

use NeuronAI\Chat\Enums\SourceType;
use NeuronAI\Chat\Messages\ContentBlocks\ImageContent;
use NeuronAI\Chat\Messages\ContentBlocks\TextContent;
use NeuronAI\Chat\Messages\UserMessage;
use Paperdoc\Contracts\LlmAugmenterInterface;
use Paperdoc\Llm\Dto\PageContent;

class LlmAugmenter implements LlmAugmenterInterface
{
    /** @var array{provider: string, model: string, api_key?: string, base_url?: string, options?: array<string, mixed>} */
    private array $config;

    /**
     * @param array{provider: string, model: string, api_key?: string, base_url?: string, options?: array<string, mixed>} $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function enhance(string $rawText, array $options = []): string
    {
        if (trim($rawText) === '') {
            return '';
        }

        $agent = $this->buildAgent();

        $message = new UserMessage(
            "Correct the following OCR text. Fix errors and clean up formatting. "
            . "Return ONLY the corrected text, nothing else.\n\n"
            . "---\n{$rawText}\n---"
        );

        $response = $agent->chat($message);

        return trim($response->getContent());
    }

    public function structureDocument(string $rawText, ?string $imagePath = null, array $options = []): array
    {
        $agent = $this->buildAgent();

        $contentBlocks = [];

        if ($imagePath !== null && file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mediaType = $this->detectMediaType($imagePath);

            $contentBlocks[] = new ImageContent($imageData, SourceType::BASE64, $mediaType);
        }

        $prompt = "Analyze this document page and extract structured content.\n";

        if (trim($rawText) !== '') {
            $prompt .= "Here is the raw OCR text as a starting point:\n---\n{$rawText}\n---\n";
        }

        $prompt .= "Extract: title (if any), paragraphs (corrected), tables (if any), and confidence score.";

        $contentBlocks[] = new TextContent($prompt);

        $message = new UserMessage($contentBlocks);

        /** @var PageContent $result */
        $result = $agent->structured($message, PageContent::class, maxRetries: 2);

        return [
            'title' => $result->title,
            'paragraphs' => $result->paragraphs,
            'tables' => $result->tables,
            'confidence' => $result->confidence,
        ];
    }

    private function buildAgent(): PaperdocAgent
    {
        $provider = ProviderFactory::make($this->config);

        return new PaperdocAgent($provider);
    }

    private function detectMediaType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            default => 'image/png',
        };
    }
}
