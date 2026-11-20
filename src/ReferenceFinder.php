<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Token, TokenTree, TokenType};

/**
 * Finds the classes, interfaces, traits and enums a file refers to. In the code, the tokenizer marks them
 * (```Token::$isClassReference```); in doc comments, ```ReferenceFinder\DoccommentParser``` finds them.
 */
#[Service]
readonly class ReferenceFinder
{
    public function __construct(
        private ReferenceFinder\DoccommentParser  $doccommentParser,
        private ReferenceFinder\ReferenceResolver $referenceResolver,
    )
    {
    }

    public function find(TokenTree $tree, ClassAnalysis $results): void
    {
        foreach ($tree as $token) {
            if ($token->is(T_DOC_COMMENT)) {
                $this->doccommentParser->processDoccomment($results, $token);
            }
            elseif ($token->is(T_EXTENDS)) {
                // One parent class, or several parent interfaces
                foreach ($this->namesAfter($token) as $name) {
                    $reference = $this->referenceResolver->resolve($results, $name->text);
                    $results->extends[$reference->label] = $reference;
                }
            }
            elseif ($token->is(T_IMPLEMENTS)) {
                foreach ($this->namesAfter($token) as $name) {
                    $reference = $this->referenceResolver->resolve($results, $name->text);
                    $results->implements[$reference->label] = $reference;
                }
            }

            if ($token->isClassReference) {
                $reference = $this->referenceResolver->resolve($results, $token->text);
                $results->uses[$reference->label] = $reference;
            }
        }
    }

    /**
     * The comma-separated class names after ```extends``` or ```implements```.
     *
     * @return Token[]
     */
    private function namesAfter(Token $keyword): array
    {
        $names = [];
        $token = $keyword->next;

        while ($token?->isClassReference || $token?->is(TokenType::COMMA)) {
            if ($token->isClassReference) {
                $names[] = $token;
            }

            $token = $token->next;
        }

        return $names;
    }
}
