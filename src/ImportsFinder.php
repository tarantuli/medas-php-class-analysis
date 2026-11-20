<?php

declare(strict_types=1);

namespace Medas\PhpClassAnalysis;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Statement, StatementTypeFinder, StatementTypes\UseClassStatement, Token, TokenTree, TokenType};

/**
 * Finds the class imports (`use` statements) of a file, including several imports in one statement
 * (`use A, B;`) and group imports (`use A\{B, C as D};`), with or without a trailing comma.
 *
 * @notFor Function and constant imports (`use function`, `use const`), and trait uses inside a class.
 */
#[Service]
readonly class ImportsFinder
{
    private const array NAME_TOKENS = [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED];

    public function __construct(
        private FqnProperties       $fqnProperties,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function find(TokenTree $tree, ClassAnalysis $results): void
    {
        foreach ($tree->statements() as $statement) {
            if ($this->typeFinder->for($statement) instanceof UseClassStatement) {
                $this->processStatement($statement, $results);
            }
        }
    }

    private function processStatement(Statement $statement, ClassAnalysis $results): void
    {
        $tokens = array_values(array_filter(
            iterator_to_array($statement, false),
            fn(Token $token) => !$token->is([T_COMMENT, T_DOC_COMMENT])
        ));

        // Skip the "use" keyword
        $index = 1;

        while (isset($tokens[$index]) && $tokens[$index]->is(self::NAME_TOKENS)) {
            $path = ltrim($tokens[$index]->text, '\\');

            ++$index;

            $isGroup = ($tokens[$index] ?? null)?->is(TokenType::BACKSLASH)
                && ($tokens[$index + 1] ?? null)?->is(TokenType::CURLY_BRACKET_OPEN);

            if ($isGroup) {
                $index = $this->processGroup($tokens, $index + 1, $path, $results);
            }
            else {
                $index = $this->processName($tokens, $index, $path, $results);
            }

            // Skip the comma between imports
            if (($tokens[$index] ?? null)?->is(TokenType::COMMA)) {
                ++$index;
            }
        }
    }

    /**
     * @param Token[] $tokens
     * @param int $index The index of the opening curly bracket of the group
     * @return int The index of the first token after the group
     */
    private function processGroup(array $tokens, int $index, string $prefix, ClassAnalysis $results): int
    {
        $closer = $tokens[$index]->matchingBracket;
        ++$index;

        while (isset($tokens[$index]) && $tokens[$index] !== $closer) {
            if ($tokens[$index]->is(self::NAME_TOKENS)) {
                $path = $prefix . '\\' . $tokens[$index]->text;
                $index = $this->processName($tokens, $index + 1, $path, $results);
            }
            else {
                // A comma, including a trailing one
                ++$index;
            }
        }

        // Skip the closing curly bracket
        return $index + 1;
    }

    /**
     * @param Token[] $tokens
     * @param string $path The imported path, without a leading backslash
     * @return int The index of the first token after the name and its alias
     */
    private function processName(array $tokens, int $index, string $path, ClassAnalysis $results): int
    {
        if (($tokens[$index] ?? null)?->is(T_AS) && isset($tokens[$index + 1])) {
            $label = $tokens[$index + 1]->text;
            $index += 2;
        }
        else {
            $label = $this->fqnProperties->getLastPart($path);
        }

        $results->imports[] = new ClassReference($label, '\\' . $path);

        return $index;
    }
}
