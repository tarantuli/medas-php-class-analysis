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
        if (str_starts_with(strtolower($label), 'namespace\\')) {
            // "namespace\Foo" is relative to the current namespace, never to an import
            return new ClassReference($label, $this->inNamespace($results, substr($label, strlen('namespace\\'))));
        }

        $firstPart = $this->fqnProperties->getFirstPart($label);

        if ($firstPart === '') {
            // It's an absolute path
            return new ClassReference($label, $label);
        }

        $resolvedFirstPart = $this->resolveImport($results, $firstPart);

        if ($resolvedFirstPart === null) {
            // It's a path relative to the namespace
            return new ClassReference($label, $this->inNamespace($results, $label));
        }
        else {
            // It's a path relative to an alias
            return new ClassReference(
                $label,
                $resolvedFirstPart . substr($label, strlen($firstPart))
            );
        }
    }

    private function inNamespace(ClassAnalysis $results, string $relativeName): string
    {
        return $results->namespace
            ? '\\' . $results->namespace . '\\' . $relativeName
            : '\\' . $relativeName;
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
