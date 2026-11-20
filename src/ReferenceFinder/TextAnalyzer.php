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

    /**
     * Doc comment tags that are followed by a type expression. `@method` may have `static` before its return type.
     * Runs of non-whitespace are taken as-is, but whitespace inside array shapes {..} and generics <..>
     * (e.g. "array<string, Invoice>") doesn't end the type, so those groups are matched with balanced recursion.
     */
    private const string TYPE_TAG_PATTERN = '/@(?:phpstan-|psalm-)?'
        . '(?:param|var|return|throws|property|property-read|property-write|method|mixin|implements|extends|use)'
        . '\s+(?:static\s+)?'
        . '((?:[^\s{}<>]++|(?<brace>\{(?:[^{}]++|(?&brace))*+})|(?<angle><(?:[^<>]++|(?&angle))*+>))++)/';

    /**
     * `@see` is followed by a class name, possibly with `::` and a member. URLs and function names (followed by `(`)
     * are skipped.
     */
    private const string SEE_TAG_PATTERN = '/@see\s+(?!\S*:\/\/)([\w\\\\]++)(?!\()/';

    /**
     * Finds the type expressions in the tags of a doc comment.
     *
     * @return array{string, string}[] Pairs of the matched text, starting at the tag, and the type expression in it
     */
    public function docTypeExpressions(string $docComment): array
    {
        $expressions = [];

        foreach ([self::TYPE_TAG_PATTERN, self::SEE_TAG_PATTERN] as $pattern) {
            if (preg_match_all($pattern, $docComment, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $expressions[] = [$match[0], $match[1]];
                }
            }
        }

        return $expressions;
    }

    /** @return string[] */
    public function extractDocTypeNames(string $type): array
    {
        $names = [];

        // Extract type names from array shape syntax: array{T1, T2, key: T3, key?: T4}
        $type = preg_replace_callback('/\{([^}]+)}/', function (array $m) use (&$names): string {
            foreach (explode(',', $m[1]) as $entry) {
                // Strip optional named key prefix, e.g. "key: Type" or "key?: Type"
                $entry = preg_replace('/^\s*[\w-]+\??\s*:\s*/', '', trim($entry));

                array_push($names, ...$this->extractDocTypeNames($entry));
            }

            return '';
        }, $type);

        // Extract type names from generic syntax: Collection<TypeA, TypeB>
        $type = preg_replace_callback('/<([^>]+)>/', function (array $m) use (&$names): string {
            foreach (explode(',', $m[1]) as $entry) {
                array_push($names, ...$this->extractDocTypeNames(trim($entry)));
            }

            return '';
        }, $type);

        foreach (preg_split('/[|&]/', $type) as $part) {
            $part = trim($part);

            // Strip nullable prefix
            if (str_starts_with($part, '?')) {
                $part = substr($part, 1);
            }

            while (str_ends_with($part, '[]')) {
                $part = substr($part, 0, -2);
            }

            if ($part !== '') {
                $names[] = $part;
            }
        }

        return $names;
    }

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
