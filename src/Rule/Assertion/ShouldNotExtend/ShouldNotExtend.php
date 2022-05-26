<?php

namespace PHPat\Rule\Assertion\ShouldNotExtend;

use PHPat\Rule\Assertion\Assertion;
use PHPat\Rule\Assertion\AssertionType;
use PHPat\Statement\Builder\StatementBuilderFactory;
use PHPStan\Reflection\ReflectionProvider;

abstract class ShouldNotExtend extends Assertion
{
    public function __construct(
        StatementBuilderFactory $statementBuilderFactory,
        ReflectionProvider $reflectionProvider
    ) {
        parent::__construct(__CLASS__, $statementBuilderFactory, $reflectionProvider);
    }

    protected function getAssertionType(): string
    {
        return AssertionType::SHOULD_NOT;
    }

    protected function getMessage(string $subject, string $target): string
    {
        return sprintf('%s should not extend %s', $subject, $target);
    }
}
