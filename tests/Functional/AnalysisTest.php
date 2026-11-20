<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysisTest\Functional;

use Medas\PhpClassAnalysis\{ClassAnalyser, ClassAnalysis, ClassReference};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Analyses each `tests/Analyses/<name>.php` and compares the result with `tests/Analyses/<name>.txt`.
 *
 * Imports are listed in source order. Extends, implements and uses are sorted by label, because their order depends
 * on how the analyser walks the tokens, not on the code.
 *
 * A missing `.txt` file is written and the test is marked incomplete, so it can be reviewed before it is committed.
 * Run with the environment variable `UPDATE_SNAPSHOTS=1` to rewrite every `.txt` file after an intended change, and
 * review the diff in git.
 */
class AnalysisTest extends TestCase
{
    private const string DIRECTORY = __DIR__ . '/../Analyses';

    /**
     * @return array<string, array{string}>
     */
    public static function analyses(): array
    {
        $cases = [];

        foreach (glob(self::DIRECTORY . '/*.php') as $file) {
            $cases[basename($file, '.php')] = [basename($file, '.php')];
        }

        return $cases;
    }

    #[DataProvider('analyses')]
    public function testAnalysis(string $name): void
    {
        $code = $this->normaliseLineEndings(file_get_contents(self::DIRECTORY . '/' . $name . '.php'));
        $actual = self::dump(service(ClassAnalyser::class)->analyse($code));
        $expectedFile = self::DIRECTORY . '/' . $name . '.txt';

        if (!is_file($expectedFile) || getenv('UPDATE_SNAPSHOTS')) {
            file_put_contents($expectedFile, $actual);

            self::markTestIncomplete(sprintf('Analysis %s.txt written, review it', $name));
        }

        self::assertSame($this->normaliseLineEndings(file_get_contents($expectedFile)), $actual);
    }

    public static function dump(ClassAnalysis $analysis): string
    {
        $kinds = array_keys(array_filter([
            'class' => $analysis->isClass,
            'interface' => $analysis->isInterface,
            'trait' => $analysis->isTrait,
            'enum' => $analysis->isEnum,
        ]));

        $modifiers = array_keys(array_filter([
            'abstract' => $analysis->isAbstract,
            'final' => $analysis->isFinal,
        ]));

        $lines = [
            'namespace: ' . $analysis->namespace,
            'name: ' . $analysis->name,
            'fqn: ' . $analysis->fqn,
            'kind: ' . implode(', ', $kinds),
            'modifiers: ' . implode(', ', $modifiers),
            'extension type: ' . ($analysis->extensionType ? self::reference($analysis->extensionType) : ''),
        ];

        $lists = [
            'extends' => self::sorted($analysis->extends),
            'implements' => self::sorted($analysis->implements),
            'imports' => $analysis->imports,
            'uses' => self::sorted($analysis->uses),
        ];

        foreach ($lists as $title => $references) {
            $lines[] = $title . ':';

            foreach ($references as $reference) {
                $lines[] = '    ' . self::reference($reference);
            }
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param ClassReference[] $references
     * @return ClassReference[]
     */
    private static function sorted(array $references): array
    {
        usort($references, fn(ClassReference $a, ClassReference $b) => strcmp($a->label, $b->label));

        return $references;
    }

    private static function reference(ClassReference $reference): string
    {
        return $reference->label . ' => ' . $reference->fqn;
    }

    private function normaliseLineEndings(string $text): string
    {
        // Git on Windows may check out the files with \r\n
        return str_replace("\r\n", "\n", $text);
    }
}
