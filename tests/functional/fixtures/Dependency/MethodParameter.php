<?php

namespace Tests\PHPat\functional\fixtures\Dependency;

use Tests\PHPat\functional\fixtures\AnotherSimpleClass as AliasedClass;
use Tests\PHPat\functional\fixtures\Inheritance;
use Tests\PHPat\functional\fixtures\SimpleClass;

class MethodParameter
{
    public function doSomething(
        SimpleClass $simpleClass,
        AliasedClass $aliasedClass,
        DependencyNamespaceSimpleClass $dependencyNamespaceSimpleClass,
        Inheritance\InheritanceNamespaceSimpleClass $inheritanceNamespaceSimpleClass
    ) {
    }
}
