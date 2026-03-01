<?php

declare(strict_types=1);

namespace Paperdoc\Llm;

use NeuronAI\Agent\Agent;
use NeuronAI\Providers\AIProviderInterface;

class PaperdocAgent extends Agent
{
    public function __construct(
        private readonly AIProviderInterface $aiProvider,
    ) {}

    protected function provider(): AIProviderInterface
    {
        return $this->aiProvider;
    }

    protected function instructions(): string
    {
        return <<<'PROMPT'
You are a document analysis assistant specialized in OCR post-processing. Your tasks:

1. CORRECT OCR errors: fix typos, broken words, garbled characters, and encoding artifacts.
2. STRUCTURE content: identify titles/headings, paragraphs, lists, and tables.
3. PRESERVE meaning: never add, remove, or rewrite content — only fix OCR mistakes.
4. DETECT tables: if tabular data is present, extract it as structured rows and columns.
5. ASSESS confidence: rate your confidence in the output quality from 0.0 to 1.0.

When given an image of a page along with raw OCR text:
- Use the image as the ground truth for layout and content.
- Use the OCR text as a noisy starting point to correct from.
- If the image is clear but OCR text is missing/empty, transcribe directly from the image.

Always respond in the same language as the document content.
PROMPT;
    }
}
