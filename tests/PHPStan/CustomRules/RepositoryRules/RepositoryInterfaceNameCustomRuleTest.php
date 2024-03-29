<?php

/*
 * Copyright 2005 - 2023 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 *
 */

declare(strict_types=1);

namespace Tests\PHPStan\CustomRules\RepositoryRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\RepositoryRules\RepositoryInterfaceNameCustomRule;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

beforeEach(function (): void {
    $this->scope = $this->createMock(Scope::class);
    $this->node = $this->createMock(Class_::class);
    $this->nameNodeInstanceInterface = $this->createMock(Name::class);
    $this->identifierNodeInstance = $this->createMock(Identifier::class);
    $this->node->name = $this->identifierNodeInstance;
});

it(
    'should return an error if Repository Interface name does not start with \'Read\' or \'Write\' and end with '
    . '\'RepositoryInterface\'.',
    function (): void {
        $invalidInterfaceImplementations = 'Path\To\Some\Interface';

        $this->nameNodeInstanceInterface
            ->expects($this->any())
            ->method('toString')
            ->willReturn($invalidInterfaceImplementations);

        $this->node->implements = [
            $this->nameNodeInstanceInterface,
        ];

        $this->identifierNodeInstance->name = 'DbReadHostgroupRepository';

        $expectedResult = [
            CentreonRuleErrorBuilder::message(
                "At least one Interface name must start with 'Read' or 'Write' and end with 'RepositoryInterface'."
            )->build(),
        ];

        $rule = new RepositoryInterfaceNameCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result[0]->message)->toBe($expectedResult[0]->message);
    }
);

it(
    'should not return an error if Repository Interface name does start with \'Read\' or \'Write\' and end with '
    . '\'RepositoryInterface\'.',
    function (): void {
        $validInterfaceImplementations = \Core\Application\Common\Session\Repository\ReadSessionRepositoryInterface::class;

        $this->nameNodeInstanceInterface
            ->expects($this->any())
            ->method('toString')
            ->willReturn($validInterfaceImplementations);

        $this->node->implements = [
            $this->nameNodeInstanceInterface,
        ];

        $this->identifierNodeInstance->name = 'DbReadSessionRepository';

        $rule = new RepositoryInterfaceNameCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);

it('should not return an error if Repository Interface is not implemented by Repository.', function (): void {
    $this->node->implements = [];

    $this->identifierNodeInstance->name = 'DbReadHostgroupRepository';

    $rule = new RepositoryInterfaceNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned class is not Repository.', function (): void {
    $invalidInterfaceImplementations = 'Path\To\Some\Interface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($invalidInterfaceImplementations);

    $this->node->implements = [
        $this->nameNodeInstanceInterface,
    ];

    $this->identifierNodeInstance->name = 'SomeClassName';

    $rule = new RepositoryInterfaceNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
