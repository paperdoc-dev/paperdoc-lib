<?php

declare(strict_types=1);

namespace Pagina\Document;

use Pagina\Contracts\DocumentElementInterface;

class PageBreak implements DocumentElementInterface
{
    public function getType(): string { return 'page_break'; }
}
