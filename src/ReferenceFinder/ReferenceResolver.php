<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\ReferenceFinder;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\{
    ClassAnalysis,
    ClassReference,
    Exceptions\ImportLabelCaseMismatch,
    FqnProperties
};

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

        $resolvedFirstPart = $this->resolveImport($results, $firstPart);

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

    public function resolveImport(ClassAnalysis $results, string $label): string|null
    {
        foreach ($results->imports as $import) {
            if ($import->label === $label) {
                return $import->fqn;
            }

            if (mb_strtolower($import->label) === mb_strtolower($label)) {
                throw new ImportLabelCaseMismatch($label, $import->label);
            }
        }

        return null;
    }
}
