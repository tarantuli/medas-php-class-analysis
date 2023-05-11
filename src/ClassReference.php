<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

class ClassReference
{
    public function __construct(
        public readonly string $label,
        public readonly string $fqn,
    )
    {
    }
}
