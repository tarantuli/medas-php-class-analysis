<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\ReferenceFinder;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\{ClassAnalysis, ClassReference, FqnProperties};

#[Service]
readonly class ReferenceResolver
{
    public function __construct(
        private FqnProperties $fqnProperties,
    )
    {
    }

    public function resolve(ClassAnalysis $results, string $label): ClassReference
    {
        $firstPart = $this->fqnProperties->getFirstPart($label);

        if ($firstPart === '') {
            // It's an absolute path
            return new ClassReference($label, $label);
        }

        $resolvedFirstPart = $results->resolveImport($firstPart);

        if ($resolvedFirstPart === null) {
            // It's a path relative to the namespace
            $fqn = $results->namespace
                ? '\\' . $results->namespace . '\\' . $label
                : '\\' . $label;

            return new ClassReference($label, $fqn);
        }
        else {
            // It's a path relative to an alias
            return new ClassReference(
                $label,
                $resolvedFirstPart . substr($label, strlen($firstPart))
            );
        }
    }
}
