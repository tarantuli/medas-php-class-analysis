<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysisTest\Functional;

use Medas\PhpClassAnalysis\ClassAnalyser;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertCount;

class DocblockArrayTypeTest extends TestCase
{
    public function testDocblockArray(): void
    {
        $php = <<<'PHP'
<?php

declare(strict_types=1);

namespace Medas\StorageManager\UnitOfWork;

use Medas\StorageManager\Interfaces\Storage;

class UnitOfWorkDocblockArrayType
{
    /** @var \SplObjectStorage<Storage> */
    private \SplObjectStorage $storages;
}

PHP;
        $analysis = service(ClassAnalyser::class)->analyse($php);

        self::assertCount(2, $analysis->uses);
    }
}
