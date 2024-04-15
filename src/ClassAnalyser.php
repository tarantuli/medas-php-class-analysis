<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{AdditionalTokensDefiner, TokenTree, TreeBuilder};

#[Service]
readonly class ClassAnalyser
{
    public function __construct(
        AdditionalTokensDefiner $additionalTokensDefiner,
        private ImportsFinder   $importsFinder,
        private NameFinder      $nameFinder,
        private ReferenceFinder $referenceFinder,
        private TreeBuilder     $treeBuilder,
    )
    {
        $additionalTokensDefiner->define();
    }

    public function analyseClassByName(string $class): ClassAnalysis
    {
        return $this->analyseClass(new \ReflectionClass($class));
    }

    public function analyseClass(\ReflectionClass $class): ClassAnalysis
    {
        return $this->analyse(file_get_contents($class->getFileName()));
    }

    public function analyse(string $code): ClassAnalysis
    {
        $tree = $this->treeBuilder->fromCode($code);

        return $this->analyseTokenTree($tree);
    }

    public function analyseTokenTree(TokenTree $tree): ClassAnalysis
    {
        $results = new ClassAnalysis();

        $this->importsFinder->find($tree, $results);
        $this->nameFinder->find($tree, $results);
        $this->referenceFinder->find($tree, $results);

        return $results;
    }
}
