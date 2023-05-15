<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysisTest\Functional;

use Medas\PhpClassAnalysis\ClassAnalyser;
use Medas\PhpClassAnalysisTest\MockUps\GenericTypes\Books;
use PHPUnit\Framework\TestCase;

class GenericTypesTest extends TestCase
{
    public function testExtendsType(): void
    {
        $analysis = service(ClassAnalyser::class)->analyseClassByName(Books::class);

        diedump($analysis);
    }
}
