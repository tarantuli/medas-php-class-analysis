<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{
    Contexts\MethodParameters,
    Contexts\MethodReturnType,
    StatementTypeFinder,
    StatementTypes\ClassDeclaration,
    StatementTypes\UseTraitStatement,
    Token,
    TokenTree
};

#[Service]
class ReferenceFinder
{
    private const REFERENCE_TYPES = [T_STRING, T_NAME_QUALIFIED, T_NAME_RELATIVE, T_NAME_FULLY_QUALIFIED];

    public function __construct(
        private readonly FqnProperties       $fqnProperties,
        private readonly StatementTypeFinder $statementTypeFinder,
    )
    {
    }

    public function find(TokenTree $tree, ClassAnalysis $results): void
    {
        foreach ($tree as $token) {
            if ($token->is(T_DOC_COMMENT)) {
                $this->processDoccomment($results, $token);
            }

            if ($token->is(T_EXTENDS)) {
                // Class extension declaration, could be multiple (in interfaces)
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $extensionToken) {
                    $this->addExtends($results, $extensionToken);
                    $this->addUsage($results, $extensionToken);
                }
            }

            if ($token->is(T_IMPLEMENTS)) {
                // Class implementation declaration, could be multiple
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $implementToken) {
                    $this->addImplements($results, $implementToken);
                    $this->addUsage($results, $implementToken);
                }
            }

            if ($token->is([T_NEW, T_INSTANCEOF])) {
                // Object instantiaion or instanceof comparison
                $this->addUsage($results, $token->next);
            }

            if ($token->is(T_USE) && $this->statementTypeFinder->for($token->statement) instanceof UseTraitStatement) {
                // A use trait statement, could be multiple
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $useToken) {
                    $this->addUsage($results, $useToken);
                }
            }

            if ($token->next && $token->next->is(T_DOUBLE_COLON)) {
                // "ClassName::..."
                $this->addUsage($results, $token);
            }

            if ($token->context instanceof MethodParameters || $token->context instanceof MethodReturnType) {
                // Parameter type or return type
                foreach ($this->gatherSeparatedTokens($token, T_PIPE) as $declarationToken) {
                    $this->addUsage($results, $declarationToken);
                }
            }

            if ($token->inAttribute) {
                // Name within an attribute
                $this->addUsage($results, $token);
            }

            if ($token->next && $token->next->is(T_VARIABLE)) {
                // ClassName $...
                foreach ($this->gatherBackwardsSeparatedTokens($token, T_PIPE) as $typeToken) {
                    $this->addUsage($results, $typeToken);
                }
            }
            elseif ($token->previous && $token->previous->previous && $token->previous->previous->is(T_CATCH)) {
                // catch (ClassName) without variable
                $this->addUsage($results, $token);
            }

            // "): <type>" in lambda functions
            $previousToken = $token;

            while ($previousToken = $previousToken->previous) {
                if ($previousToken->is(T_ROUND_BRACKET_CLOSE)) {
                    $this->addUsage($results, $token);

                    break;
                }

                if ($previousToken->is([T_STRING, T_COLON, T_PIPE, T_NAME_FULLY_QUALIFIED])) {
                    continue;
                }

                break;
            }
        }
    }

    private function getReference(ClassAnalysis $results, Token $token): ClassReference
    {
        return $this->resolveReference($results, $token->text);
    }

    private function resolveReference(ClassAnalysis $results, string $label): ClassReference
    {
        $firstPart = $this->fqnProperties->getFirstPart($label);

        if ($firstPart === '') {
            // It's an absolute path
            return new ClassReference($label, $label);
        }

        $resolvedFirstPart = $results->resolveImport($firstPart);

        if ($resolvedFirstPart === null) {
            // It's a path relative to the namespace
            $fqn = $results->namespace ? '\\' . $results->namespace . '\\' . $label : '\\' . $label;

            return new ClassReference($label, $fqn);
        }
        else {
            // It's a path relative to an alias
            return new ClassReference($label, $resolvedFirstPart . substr($label, strlen($firstPart)));
        }
    }

    private function addUsage(ClassAnalysis $results, Token $token): void
    {
        if ($this->couldBeClassName($token)) {
            $reference = $this->getReference($results, $token);
            $results->uses[$reference->label] = $reference;
        }
    }

    private function gatherSeparatedTokens(Token $token, int|string $separator): array
    {
        $tokens = [];

        do {
            $tokens[] = $token;
            $token = $token->next->next;
        } while ($token && $token->previous->is($separator));

        return $tokens;
    }

    private function gatherBackwardsSeparatedTokens(Token $token, int|string $separator): array
    {
        $tokens = [];

        do {
            $tokens[] = $token;

            if (!$token->previous || !$token->previous->previous) {
                break;
            }

            $token = $token->previous->previous;
        } while ($token->next->is($separator));

        return $tokens;
    }

    private function addExtends(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->getReference($results, $token);
        $results->extends[$reference->label] = $reference;
    }

    private function addImplements(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->getReference($results, $token);
        $results->implements[$reference->label] = $reference;
    }

    private function couldBeClassName(Token $token): bool
    {
        return $token->is(self::REFERENCE_TYPES) && $this->textCouldBeClassName($token->text);
    }

    private function textCouldBeClassName(string $text): bool
    {
        return !in_array($text, PhpKeywords::ALL, true) && !in_array($text, PhpKeywords::INTERNAL_TYPES, true);
    }

    private function processDoccomment(ClassAnalysis $results, Token $token): void
    {
        if ($this->statementTypeFinder->for($token->statement) instanceof ClassDeclaration) {
            // The class doccomment
            if (preg_match('/@extends\s+([\w\\\]+)<([\w\\\]+)>/', $token->text, $matches)) {
                // Process the extension type
                $results->extensionType = $this->resolveReference($results, $matches[2]);

                if ($this->textCouldBeClassName($matches[2])) {
                    $results->uses[$matches[2]] = $results->extensionType;
                }

                // Process the extended class itself
                $results->uses[$matches[1]] = $this->resolveReference($results, $matches[1]);
            }
        }

        if (preg_match('/@(?:param|var|return|throws)\s+(\S+)/', $token->text, $matches)) {
            foreach (explode('|', $matches[1]) as $reference) {
                while (str_ends_with($reference, '[]')) {
                    $reference = substr($reference, 0, -2);
                }

                if ($this->textCouldBeClassName($reference)) {
                    $results->uses[$reference] = $this->resolveReference($results, $reference);
                }
            }
        }
    }
}
