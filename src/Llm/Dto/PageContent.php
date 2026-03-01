<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Dto;

use NeuronAI\StructuredOutput\SchemaProperty;

class PageContent
{
    #[SchemaProperty(description: 'The page or section title, empty string if none.', required: true)]
    public string $title = '';

    /**
     * @var string[]
     */
    #[SchemaProperty(description: 'Ordered list of corrected and cleaned paragraphs.', required: true)]
    public array $paragraphs = [];

    /**
     * Each table is a 2D array: rows of cells (string values).
     *
     * @var array<int, string[][]>
     */
    #[SchemaProperty(description: 'Tables found in the page, each as a 2D array of string cells.', required: false)]
    public array $tables = [];

    #[SchemaProperty(description: 'Confidence score from 0.0 to 1.0 indicating OCR/LLM output quality.', required: true)]
    public float $confidence = 0.0;
}
