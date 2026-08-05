<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysisTest\Functional;

use Medas\PhpClassAnalysis\ClassAnalyser;
use PHPUnit\Framework\TestCase;

class DocblockGenericSpacingTest extends TestCase
{
    public function testSpaceInsideGenericTypeArgumentIsFound(): void
    {
        $php = <<<'PHP'
<?php

declare(strict_types=1);

namespace Medas\StorageManager\UnitOfWork;

use Medas\StorageManager\Invoice;

class UnitOfWorkSpacedGeneric
{
    /** @var array<string, Invoice> $draftsByAccount */
    private array $draftsByAccount;
}

PHP;
        $analysis = service(ClassAnalyser::class)->analyse($php);

        self::assertArrayHasKey('Invoice', $analysis->uses);
        self::assertEquals(
            '\\Medas\\StorageManager\\Invoice',
            $analysis->uses['Invoice']->fqn
        );
    }

    public function testSpaceInsideNestedGenericTypeArgumentIsFound(): void
    {
        $php = <<<'PHP'
<?php

declare(strict_types=1);

namespace Medas\StorageManager\UnitOfWork;

use Medas\StorageManager\Invoice;

class UnitOfWorkNestedSpacedGeneric
{
    /** @var array<string, array<int, Invoice>> $invoicesByAccount */
    private array $invoicesByAccount;
}

PHP;
        $analysis = service(ClassAnalyser::class)->analyse($php);

        self::assertArrayHasKey('Invoice', $analysis->uses);
    }
}
