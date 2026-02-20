<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\PhpTokenizer\Token;

class TokenIsNotChainedWell extends BaseException
{
    public function __construct(Token $token)
    {
        parent::__construct($token->text);
    }

    public function pattern(): string
    {
        return 'token %s is not chained well (either or both of its neighbours don\'t point to it)';
    }
}
