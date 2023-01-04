<?php

/*
 * Copyright 2005 - 2022 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 */

declare(strict_types=1);

namespace Tests\PHPStan\CustomRules\RepositoryRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\RepositoryRules\RepositoryMethodReturnCustomRule;
use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

beforeEach(function () {
    $this->node = $this->createMock(Class_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->classMethodNodeInstance = $this->createMock(ClassMethod::class);
    $this->identifierNodeInstanceForClass = $this->createMock(Identifier::class);
    $this->identifierNodeInstanceForClassMethod = $this->createMock(Identifier::class);
    $this->identifierNodeInstanceForMethodReturnType = $this->createMock(Identifier::class);
    $this->nullableTypeNodeInstance = $this->createMock(NullableType::class);
    $this->node->name = $this->identifierNodeInstanceForClass;
    $this->classMethodNodeInstance->name = $this->identifierNodeInstanceForClassMethod;
    $this->arrayOfClassMethods = [
        $this->classMethodNodeInstance
    ];
    $this->node
        ->expects($this->any())
        ->method('getMethods')
        ->willReturn($this->arrayOfClassMethods);
});

it(
    'should return an error if Repository\'s find method does not return neither null, an object nor '
        . 'an array of objects.',
    function () {
        $this->identifierNodeInstanceForClassMethod->name = 'findHostId';

        $this->classMethodNodeInstance
            ->expects($this->any())
            ->method('getReturnType')
            ->willReturn($this->identifierNodeInstanceForMethodReturnType);

        $this->identifierNodeInstanceForMethodReturnType
            ->expects($this->any())
            ->method('toString')
            ->willReturn('string');

        $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

        $expectedResult = [
            CentreonRuleErrorBuilder::message(
                $this->identifierNodeInstanceForClassMethod->name .
                " must return null, an object, an iterable or an array of objects."
            )->line($this->classMethodNodeInstance->getLine())->build(),
        ];

        $rule = new RepositoryMethodReturnCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result[0]->message)->toBe($expectedResult[0]->message);
    }
);

it(
    'should return an error if Repository\'s get method does not return neither an object nor an array of objects.',
    function () {
        $this->identifierNodeInstanceForClassMethod->name = 'getHostId';

        $this->classMethodNodeInstance
            ->expects($this->any())
            ->method('getReturnType')
            ->willReturn($this->identifierNodeInstanceForMethodReturnType);

        $this->identifierNodeInstanceForMethodReturnType
            ->expects($this->any())
            ->method('toString')
            ->willReturn('string');

        $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

        $expectedResult = [
            CentreonRuleErrorBuilder::message(
                $this->identifierNodeInstanceForClassMethod->name .
                " must return an object or an array of objects."
            )->line($this->classMethodNodeInstance->getLine())->build(),
        ];

        $rule = new RepositoryMethodReturnCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result[0]->message)->toBe($expectedResult[0]->message);
    }
);

it('should not return an error if Repository\'s find method returns an object.', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'findHost';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->nullableTypeNodeInstance);

    $this->nullableTypeNodeInstance->type = $this->identifierNodeInstanceForMethodReturnType;

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('Host');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $resultForObject = $rule->processNode($this->node, $this->scope);
    expect($resultForObject)->toBeArray();
    expect($resultForObject)->toBeEmpty();
});

it('should not return an error if Repository\'s find method returns an array.', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'findHost';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('array');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $resultForObject = $rule->processNode($this->node, $this->scope);
    expect($resultForObject)->toBeArray();
    expect($resultForObject)->toBeEmpty();
});

it('should return no error if Repository\'s find method returns an iterable', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'findHost';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('iterable');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $resultForObject = $rule->processNode($this->node, $this->scope);
    expect($resultForObject)->toBeArray();
    expect($resultForObject)->toBeEmpty();
});

it('should not return an error if Repository\'s method get returns an object.', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'getHost';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('Host');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if Repository\'s method get returns an array.', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'getHost';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('array');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned Repository\'s method is neither find nor get.', function () {
    $this->identifierNodeInstanceForClassMethod->name = 'myPrecious';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('string');

    $this->identifierNodeInstanceForClass->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryMethodReturnCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned class is not a Repository.', function () {

    $this->identifierNodeInstanceForClassMethod->name = 'getPrecious';

    $this->classMethodNodeInstance
        ->expects($this->any())
        ->method('getReturnType')
        ->willReturn($this->identifierNodeInstanceForMethodReturnType);

    $this->identifierNodeInstanceForMethodReturnType
        ->expects($this->any())
        ->method('toString')
        ->willReturn('string');

    $this->identifierNodeInstanceForClass->name = 'SomeClassName';

    $rule = new RepositoryMethodReturnCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
