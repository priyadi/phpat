<?php

declare(strict_types=1);

namespace PHPat\Statement\Builder;

use PHPat\Selector\SelectorInterface;
use PHPat\Test\RelationRule;
use PHPat\Test\Rule;
use PhpParser\Node;
use PHPStan\Rules\Rule as PHPStanRule;

abstract class DeclarationStatementBuilder implements StatementBuilder
{
    /** @var array<array{SelectorInterface, array<SelectorInterface>}> */
    protected $statements = [];
    /** @var array<RelationRule> */
    protected array $rules;

    /**
     * @param array<RelationRule> $rules
     */
    final public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function build(): array
    {
        $params = $this->extractCurrentAssertion($this->rules);

        foreach ($params as $param) {
            $this->addStatement($param[0], $param[1]);
        }

        return $this->statements;
    }

    /**
     * @return class-string<PHPStanRule<Node>>
     */
    abstract protected function getAssertionClassname(): string;

    /**
     * @param array<SelectorInterface> $subjectExcludes
     */
    private function addStatement(
        SelectorInterface $subject,
        array $subjectExcludes
    ): void {
        $this->statements[] = [$subject, $subjectExcludes];
    }

    /**
     * @param array<Rule> $rules
     * @return array<array{SelectorInterface, array<SelectorInterface>}>
     */
    private function extractCurrentAssertion(array $rules): array
    {
        $result = [];
        foreach ($rules as $rule) {
            if ($rule->getAssertion() === $this->getAssertionClassname()) {
                foreach ($rule->getSubjects() as $selector) {
                    $result[] = [$selector, $rule->getSubjectExcludes()];
                }
            }
        }

        return $result;
    }
}
