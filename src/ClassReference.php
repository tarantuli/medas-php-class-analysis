<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

readonly class ClassReference
{
    public function __construct(
        public string $label,
        public string $fqn,
    )
    {
    }
}
