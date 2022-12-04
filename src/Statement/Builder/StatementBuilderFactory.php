<?php

declare(strict_types=1);

namespace PHPat\Statement\Builder;

use InvalidArgumentException;
use PHPat\Rule\Assertion\Declaration\DeclarationAssertion;
use PHPat\Rule\Assertion\Relation\RelationAssertion;
use PHPat\Test\Rule;
use PHPat\Test\TestParser;

class StatementBuilderFactory
{
    /** @var array<Rule> */
    private array $rules;

    public function __construct(TestParser $testParser)
    {
        $this->rules = $testParser();
    }

    public function create(string $classname): StatementBuilder
    {
        $lastSeparatorPos = strrpos($classname, '\\');
        $classnamePos     = $lastSeparatorPos !== false ? $lastSeparatorPos + 1 : 0;

        if (is_a($classname, RelationAssertion::class, true)) {
            /** @var class-string<RelationStatementBuilder> $statementBuilder */
            $statementBuilder = sprintf(
                '%s\\Relation\\%sStatementBuilder',
                __NAMESPACE__,
                substr($classname, $classnamePos)
            );
        } elseif (is_a($classname, DeclarationAssertion::class, true)) {
            /** @var class-string<DeclarationStatementBuilder> $statementBuilder */
            $statementBuilder = sprintf(
                '%s\\Declaration\\%sStatementBuilder',
                __NAMESPACE__,
                substr($classname, $classnamePos)
            );
        } else {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid statement builder', $classname));
        }

        return new $statementBuilder($this->rules);
    }
}
