<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

class ClassAnalysis
{
    public string $namespace = '';
    public string|null $name = null;
    public string $fqn = '';

    //---
    /** @var ClassReference[] */
    public array $extends = [];
    public ClassReference|null $extensionType = null;

    /** @var ClassReference[] */
    public array $implements = [];

    //---
    public bool $isClass = false;
    public bool $isInterface = false;
    public bool $isTrait = false;
    public bool $isEnum = false;

    //---
    public bool $isAbstract = false;
    public bool $isFinal = false;

    /** @var ClassReference[] */
    public array $uses = [];

    /** @var ClassReference[] */
    public array $imports = [];

    public function resolveImport(string $label): string|null
    {
        foreach ($this->imports as $import) {
            if ($import->label === $label) {
                return $import->fqn;
            }
        }

        return null;
    }
}
