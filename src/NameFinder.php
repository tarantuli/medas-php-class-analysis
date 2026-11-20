<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Declaration, DeclarationKind, TokenTree};

/**
 * Finds the namespace of the file, the class name, the class type (class, interface, trait, enum)
 * and modifiers (abstract, final).
 */
#[Service]
readonly class NameFinder
{
    public function find(TokenTree $tree, ClassAnalysis $results): void
    {
        foreach ($tree->statements() as $statement) {
            $first = $statement->firstNonCommentToken();

            // "namespace Name;" or "namespace Name {", but not the global "namespace {"
            if ($first?->is(T_NAMESPACE) && $first->next?->is([T_STRING, T_NAME_QUALIFIED])) {
                $results->namespace = $first->next->text;
            }

            if ($statement->declaration?->kind->isClassLike()) {
                $this->processDeclaration($statement->declaration, $results);

                // We found both the namespace and the name, we're done!
                return;
            }
        }
    }

    private function processDeclaration(Declaration $declaration, ClassAnalysis $results): void
    {
        $results->name = $declaration->name;

        $results->fqn = $results->namespace
            ? '\\' . $results->namespace . '\\' . $results->name
            : '\\' . $results->name;

        match ($declaration->kind) {
            DeclarationKind::Class_ => $results->isClass = true,
            DeclarationKind::Interface => $results->isInterface = true,
            DeclarationKind::Trait => $results->isTrait = true,
            DeclarationKind::Enum => $results->isEnum = true,
        };

        $results->isFinal = $declaration->hasModifier('final');
        $results->isAbstract = $declaration->hasModifier('abstract');
    }
}
