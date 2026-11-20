<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{
    Contexts\MethodParameters,
    Contexts\MethodReturnType,
    StatementTypeFinder,
    StatementTypes\UseTraitStatement,
    Token,
    TokenTree
};

#[Service]
readonly class ReferenceFinder
{
    private const array TYPE_EXPRESSION_TOKENS = [
        T_STRING,
        T_NAME_QUALIFIED,
        T_NAME_FULLY_QUALIFIED,
        T_VERTICAL_BAR,
        T_AMPERSAND,
        T_QUESTION_MARK,
    ];

    public function __construct(
        private ReferenceFinder\DoccommentParser  $doccommentParser,
        private ReferenceFinder\ReferenceResolver $referenceResolver,
        private ReferenceFinder\TextAnalyzer      $textAnalyzer,
        private StatementTypeFinder               $statementTypeFinder,
    )
    {
    }

    public function find(TokenTree $tree, ClassAnalysis $results): void
    {
        // The number of brackets opened inside the current attribute; names at depth 0 are attribute names
        $attributeDepth = 0;

        foreach ($tree as $token) {
            $attributeDepth = $this->updateAttributeDepth($token, $attributeDepth);

            if ($token->is(T_DOC_COMMENT)) {
                $this->doccommentParser->processDoccomment($results, $token);
            }
            elseif ($token->is(T_EXTENDS)) {
                // Class extension declaration, could be multiple (in interfaces)
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $extensionToken) {
                    $this->addExtends($results, $extensionToken);
                    $this->addUsage($results, $extensionToken);
                }
            }
            elseif ($token->is(T_IMPLEMENTS)) {
                // Class implementation declaration, could be multiple
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $implementToken) {
                    $this->addImplements($results, $implementToken);
                    $this->addUsage($results, $implementToken);
                }
            }
            elseif ($token->is([T_NEW, T_INSTANCEOF])) {
                // Object instantiaion or instanceof comparison
                $this->addUsage($results, $token->next);
            }
            elseif (
                $token->is(T_USE)
                && $this->statementTypeFinder->for($token->statement) instanceof UseTraitStatement
            ) {
                // A use trait statement, could be multiple
                foreach ($this->gatherSeparatedTokens($token->next, T_COMMA) as $useToken) {
                    $this->addUsage($results, $useToken);
                }
            }
            elseif (
                ($token->context instanceof MethodParameters && $token->inTypeDeclaration)
                || $token->context instanceof MethodReturnType
            ) {
                // Parameter type or return type. Default values aren't types: a constant there is no class reference.
                foreach ($this->gatherSeparatedTokens($token, [T_VERTICAL_BAR, T_AMPERSAND]) as $declarationToken) {
                    $this->addUsage($results, $declarationToken);
                }
            }
            elseif ($token->inAttribute) {
                // The name of an attribute, or a class in "Class::..." in its arguments, but not a constant argument
                if ($attributeDepth === 0 || $token->next?->is(T_DOUBLE_COLON)) {
                    $this->addUsage($results, $token);
                }
            }
            elseif (
                $token->next
                && $token->next->is(T_DOUBLE_COLON)
                && (!$token->previous || !$token->previous->is(T_OBJECT_OPERATOR))
            ) {
                // "ClassName::...", but not "$this->propertyName::...)"
                $this->addUsage($results, $token);
            }
            elseif ($token->next && $token->next->is(T_VARIABLE)) {
                // ClassName $...
                foreach ($this->gatherBackwardsSeparatedTokens($token, [T_VERTICAL_BAR, T_AMPERSAND]) as $typeToken) {
                    $this->addUsage($results, $typeToken);
                }
            }
            elseif ($token->previous && $token->previous->previous && $token->previous->previous->is(T_CATCH)) {
                // catch (ClassName) without a variable
                foreach ($this->gatherSeparatedTokens($token, [T_VERTICAL_BAR, T_AMPERSAND]) as $declarationToken) {
                    $this->addUsage($results, $declarationToken);
                }
            }

            if ($this->isClosureReturnType($token)) {
                // "): <type> {" and "): <type> =>" in lambda functions
                $this->addUsage($results, $token);
            }
        }
    }

    /**
     * The return type of a closure or arrow function is preceded by "):" and followed by "{" or "=>". Requiring both
     * keeps out a constant or function call in the "else" part of a ternary, like `$a ? b() : C;`.
     */
    private function isClosureReturnType(Token $token): bool
    {
        $previous = $token->previous;

        while ($previous?->is(self::TYPE_EXPRESSION_TOKENS)) {
            $previous = $previous->previous;
        }

        if (!$previous?->is(T_COLON) || !$previous->previous?->is(T_ROUND_BRACKET_CLOSE)) {
            return false;
        }

        $next = $token->next;

        while ($next?->is(self::TYPE_EXPRESSION_TOKENS)) {
            $next = $next->next;
        }

        return $next?->is([T_CURLY_BRACKET_OPEN, T_DOUBLE_ARROW]) ?? false;
    }

    private function updateAttributeDepth(Token $token, int $depth): int
    {
        if ($token->is(T_ATTRIBUTE)) {
            return 0;
        }

        if (!$token->inAttribute || $token->closesAttribute) {
            return $depth;
        }

        if ($token->is([T_ROUND_BRACKET_OPEN, T_SQUARE_BRACKET_OPEN])) {
            return $depth + 1;
        }

        if ($token->is([T_ROUND_BRACKET_CLOSE, T_SQUARE_BRACKET_CLOSE])) {
            return $depth - 1;
        }

        return $depth;
    }

    private function addExtends(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->referenceResolver->resolve($results, $token->text);
        $results->extends[$reference->label] = $reference;
    }

    private function addImplements(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->referenceResolver->resolve($results, $token->text);
        $results->implements[$reference->label] = $reference;
    }

    private function gatherBackwardsSeparatedTokens(Token $token, int|string|array $separator): array
    {
        $tokens = [];

        do {
            $tokens[] = $token;

            if (!$token->previous || !$token->previous->previous) {
                break;
            }

            $token = $token->previous->previous;
        } while ($token->next && $token->next->is($separator));

        return $tokens;
    }

    private function gatherSeparatedTokens(Token $token, int|string|array $separator): array
    {
        $tokens = [];

        do {
            $tokens[] = $token;
            $token = $token->next?->next;

            if ($token && $token->next && $token->next->next && $token->next !== $token->next->next->previous) {
                throw new Exceptions\TokenIsNotChainedWell($token->next);
            }
        } while ($token && $token->previous->is($separator));

        return $tokens;
    }

    private function addUsage(ClassAnalysis $results, Token $token): void
    {
        if ($this->isNamedArgument($token)) {
            return;
        }

        if ($this->textAnalyzer->tokenCouldBeClassName($token)) {
            $reference = $this->referenceResolver->resolve($results, $token->text);
            $results->uses[$reference->label] = $reference;
        }
    }

    /**
     * "name:" directly after "(" or "," is a named argument, e.g. new Operation(requestBody: ...).
     * A ternary "? Foo :", "case FOO:" and goto labels are not preceded by "(" or ",".
     */
    private function isNamedArgument(Token $token): bool
    {
        return $token->is(T_STRING)
            && $token->next?->is(T_COLON)
            && $token->previous?->is([T_ROUND_BRACKET_OPEN, T_COMMA]);
    }
}
