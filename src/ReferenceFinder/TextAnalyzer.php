<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis\ReferenceFinder;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\PhpKeywords;
use Medas\PhpTokenizer\Token;

#[Service]
readonly class TextAnalyzer
{
    private const array REFERENCE_TYPES = [
        T_STRING,
        T_NAME_QUALIFIED,
        T_NAME_RELATIVE,
        T_NAME_FULLY_QUALIFIED,
    ];

    public function tokenCouldBeClassName(Token $token): bool
    {
        return $token->is(self::REFERENCE_TYPES) && $this->couldBeClassName($token->text);
    }

    public function couldBeClassName(string $text): bool
    {
        return !in_array($text, PhpKeywords::ALL, true)
            && !in_array($text, PhpKeywords::INTERNAL_TYPES, true)
            && preg_match('/^[\w\\\\]+$/', $text);
    }
}
