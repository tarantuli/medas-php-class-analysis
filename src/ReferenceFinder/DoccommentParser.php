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

        foreach ($this->textAnalyzer->docTypeExpressions($token->text) as [, $typeExpression]) {
            foreach ($this->textAnalyzer->extractDocTypeNames($typeExpression) as $reference) {
                if ($this->textAnalyzer->couldBeClassName($reference)) {
                    $results->uses[$reference] = $this->referenceResolver->resolve($results, $reference);
                }
            }
        }
    }
}
