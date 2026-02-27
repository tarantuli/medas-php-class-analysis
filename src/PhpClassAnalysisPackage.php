<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\PhpTokenizer\PhpTokenizerPackage;

class PhpClassAnalysisPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            PhpTokenizerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
