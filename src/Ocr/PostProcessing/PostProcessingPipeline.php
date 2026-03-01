<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

class PostProcessingPipeline
{
    /** @var PostProcessorInterface[] */
    private array $layers = [];

    public function addLayer(PostProcessorInterface $layer): self
    {
        $this->layers[] = $layer;

        return $this;
    }

    /**
     * Run all layers in order and return the final result.
     *
     * @param  array $context Shared context — layers may read/write keys like
     *                        'language', 'entities', 'structure', 'corrections'
     */
    public function process(string $text, array $context = []): string
    {
        foreach ($this->layers as $layer) {
            $text = $layer->process($text, $context);
        }

        return $text;
    }

    /** @return PostProcessorInterface[] */
    public function getLayers(): array
    {
        return $this->layers;
    }

    public function getContext(): array
    {
        return [];
    }
}
