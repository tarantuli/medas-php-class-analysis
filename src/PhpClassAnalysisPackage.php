<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\AsSingleton;
use Medas\PhpTokenizer\PhpTokenizerPackage;
use Medas\ServiceManager\BasePackage;

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
