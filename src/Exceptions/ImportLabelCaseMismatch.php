<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ImportLabelCaseMismatch extends BaseException
{
    public function __construct($reference, $imported)
    {
        parent::__construct($reference, $imported);
    }

    public function pattern(): string
    {
        return 'Class reference %s does not match the case of the imported class %s';
    }
}
