<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\ReferenceFinder;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\ClassAnalysis;
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\ClassDeclaration, Token};

#[Service]
readonly class DoccommentParser
{
    public function __construct(
        private ReferenceResolver   $referenceResolver,
        private StatementTypeFinder $statementTypeFinder,
        private TextAnalyzer        $textAnalyzer,
    )
    {
    }

    public function processDoccomment(ClassAnalysis $results, Token $token): void
    {
        if ($this->statementTypeFinder->for($token->statement) instanceof ClassDeclaration) {
            // The class doccomment
            if (preg_match('/@extends\s+([\w\\\]+)<([\w\\\]+)>/', $token->text, $matches)) {
                // Process the extension type
                $results->extensionType = $this->referenceResolver->resolve($results, $matches[2]);

                if ($this->textAnalyzer->couldBeClassName($matches[2])) {
                    $results->uses[$matches[2]] = $results->extensionType;
                }

                // Process the extended class itself
                $results->uses[$matches[1]] = $this->referenceResolver->resolve(
                    $results,
                    $matches[1]
                );
            }
        }

        if (preg_match_all(
            '/@(?:param|var|return|throws)\s+((?:[^{}\s]+|\{[^}]*})+)/',
            $token->text,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                foreach ($this->extractDocTypeNames($match[1]) as $reference) {
                    if ($this->textAnalyzer->tokenCouldBeClassName($reference)) {
                        $results->uses[$reference] = $this->referenceResolver->resolve(
                            $results,
                            $reference
                        );
                    }
                }
            }
        }
    }

    private function extractDocTypeNames(string $type): array
    {
        $names = [];

        // Extract type names from array shape syntax: array{T1, T2, key: T3, key?: T4}
        $type = preg_replace_callback('/\{([^}]+)}/', function (array $m) use (&$names): string {
            foreach (explode(',', $m[1]) as $entry) {
                // Strip optional named key prefix, e.g. "key: Type" or "key?: Type"
                $entry = preg_replace('/^\s*[\w-]+\??\s*:\s*/', '', trim($entry));

                array_push($names, ...$this->extractDocTypeNames($entry));
            }

            return '';
        }, $type);

        // Extract type names from generic syntax: Collection<TypeA, TypeB>
        $type = preg_replace_callback('/<([^>]+)>/', function (array $m) use (&$names): string {
            foreach (explode(',', $m[1]) as $entry) {
                array_push($names, ...$this->extractDocTypeNames(trim($entry)));
            }

            return '';
        }, $type);

        foreach (preg_split('/[|&]/', $type) as $part) {
            $part = trim($part);

            // Strip nullable prefix
            if (str_starts_with($part, '?')) {
                $part = substr($part, 1);
            }

            while (str_ends_with($part, '[]')) {
                $part = substr($part, 0, -2);
            }

            if ($part !== '') {
                $names[] = $part;
            }
        }

        return $names;
    }
}
