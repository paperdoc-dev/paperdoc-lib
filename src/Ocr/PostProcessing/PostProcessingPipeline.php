<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

class PostProcessingPipeline
{
    /** @var list<PostProcessorInterface> */
    private array $layers = [];

    public function addLayer(PostProcessorInterface $layer): self
    {
        $this->layers[] = $layer;

        return $this;
    }

    /**
     * Run all layers in order and return the final result.
     *
     * @param  array<string, mixed> $context Shared context — layers may read/write keys like
     *                                       'language', 'entities', 'structure', 'corrections'
     */
    public function process(string $text, array $context = []): string
    {
        foreach ($this->layers as $layer) {
            $text = $layer->process($text, $context);
        }

        return $text;
    }

    /** @return list<PostProcessorInterface> */
    public function getLayers(): array
    {
        return $this->layers;
    }

    /** @return array<string, mixed> */
    public function getContext(): array
    {
        return [];
    }
}
