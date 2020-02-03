<?php

namespace Tests\PhpAT\unit\Rule\Assertion\Composition;

use PHPAT\EventDispatcher\EventDispatcher;
use PhpAT\Parser\AstNode;
use PhpAT\Parser\ClassName;
use PhpAT\Parser\Relation\Composition;
use PhpAT\Parser\Relation\Dependency;
use PhpAT\Parser\Relation\Inheritance;
use PhpAT\Parser\Relation\Mixin;
use PhpAT\Rule\Assertion\Composition\MustOnlyImplement;
use PhpAT\Statement\Event\StatementNotValidEvent;
use PhpAT\Statement\Event\StatementValidEvent;
use PHPUnit\Framework\TestCase;

class MustOnlyImplementTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param string $fqcnOrigin
     * @param array  $fqcnDestinations
     * @param array  $astMap
     * @param bool   $inverse
     * @param array  $expectedEvents
     */
    public function testDispatchesCorrectEvents(
        string $fqcnOrigin,
        array $fqcnDestinations,
        array $astMap,
        bool $inverse,
        array $expectedEvents
    ): void
    {
        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $class = new MustOnlyImplement($eventDispatcherMock);

        foreach ($expectedEvents as $valid) {
            $eventType = $valid ? StatementValidEvent::class : StatementNotValidEvent::class;
            $consecutive[] = [$this->isInstanceOf($eventType)];
        }

        $eventDispatcherMock
            ->expects($this->exactly(count($consecutive??[])))
            ->method('dispatch')
            ->withConsecutive(...$consecutive??[]);

        $class->validate($fqcnOrigin, $fqcnDestinations, $astMap, $inverse);
    }

    public function dataProvider(): array
    {
        return [
            [
                'Example\ClassExample',
                ['Example\InterfaceExample', 'Example\AnotherInterface'],
                $this->getAstMap(),
                false,
                [true, true, true]
            ],
            //it fails because it does not implement NotImplementedInterface
            [
                'Example\ClassExample',
                ['Example\InterfaceExample', 'Example\AnotherInterface', 'NotImplementedInterface'],
                $this->getAstMap(),
                false,
                [true, true, false, true]
            ],
            //it fails because Example\AnotherInterface is also implemented
            ['Example\ClassExample', ['Example\InterfaceExample'], $this->getAstMap(), false, [true, false]],
            //it fails because there implements 2 that are not listed and does not implement NotARealInterface
            ['Example\ClassExample', ['NotARealInterface'], $this->getAstMap(), false, [false, false, false]],
        ];
    }

    private function getAstMap(): array
    {
        return [
            new AstNode(
                new \SplFileInfo('folder/Example/ClassExample.php'),
                new ClassName('Example', 'ClassExample'),
                [
                    new Inheritance(0, new ClassName('Example', 'ParentClassExample')),
                    new Dependency(0, new ClassName('Example', 'AnotherClassExample')),
                    new Dependency(0, new ClassName('Vendor', 'ThirdPartyExample')),
                    new Composition(0, new ClassName('Example', 'InterfaceExample')),
                    new Composition(0, new ClassName('Example', 'AnotherInterface')),
                    new Mixin(0, new ClassName('Example', 'TraitExample'))
                ]
            )
        ];
    }
}