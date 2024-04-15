<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysisTest\Functional;

use Medas\PhpClassAnalysis\ClassAnalyser;
use PHPUnit\Framework\TestCase;

class Bug1Test extends TestCase
{
    public function testBug(): void
    {
        $analysis
            = service(ClassAnalyser::class)->analyse(file_get_contents(__DIR__ . '/../MockUps/Bug1/Settings.php'));

        self::assertEquals([
            'Axes\\X',
            'Axes\\Y',
            'Color',
            'DatasetCollection',
            'Visualisation',
            'Visualisations\\Line',
            'Image',
            'Legend',
            'Axes\\Y2',
            'InvalidInputException',
            'Str',
        ], array_keys($analysis->uses));
    }
}
