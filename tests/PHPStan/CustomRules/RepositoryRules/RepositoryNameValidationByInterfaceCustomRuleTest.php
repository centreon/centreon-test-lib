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
use Centreon\PHPStan\CustomRules\RepositoryRules\RepositoryNameValidationByInterfaceCustomRule;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

beforeEach(function () {
    $this->node = $this->createMock(Class_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->nameNodeInstanceInterface = $this->createMock(Name::class);
    $this->identifierNodeInstance = $this->createMock(Identifier::class);
    $this->node->name = $this->identifierNodeInstance;
    $this->node->implements = [
        $this->nameNodeInstanceInterface,
    ];
});

it('should return an error if Repository name does not match implemented Interface name.', function () {
    $interfaceImplementationName = 'Core\Application\Common\Session\Repository\ReadSessionRepositoryInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($interfaceImplementationName);

    $this->identifierNodeInstance->name = 'DbWriteSessionRepository';

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Repository name should match the implemented Interface name with exception of data storage prefix '
                . 'and \'Interface\' mention.'
        )->tip(
            'For example, Repository name: \'DbReadSessionRepository\' and implemented Interface name: '
                . '\'ReadSessionRepositoryInterface\'.'
        )->build(),
    ];

    $rule = new RepositoryNameValidationByInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if Repository name does match Interface name, but the latter is invalid.', function() {
    $interfaceImplementationName = 'Core\Application\Common\Session\Repository\DbReadSessionRepositoryInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($interfaceImplementationName);

    $this->identifierNodeInstance->name = 'DbReadSessionRepository';

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Repository name should match the implemented Interface name with exception of data storage prefix '
                . 'and \'Interface\' mention.'
        )->tip(
            'For example, Repository name: \'DbReadSessionRepository\' and implemented Interface name: '
                . '\'ReadSessionRepositoryInterface\'.'
        )->build(),
    ];

    $rule = new RepositoryNameValidationByInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if Repository name does match implemented Interface.', function () {
    $interfaceImplementationName = 'Core\Application\Common\Session\Repository\ReadSessionRepositoryInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($interfaceImplementationName);

    $this->identifierNodeInstance->name = 'DbReadSessionRepository';

    $rule = new RepositoryNameValidationByInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned class is not a Repository and Interface name is invalid.', function () {
    $interfaceImplementationName = 'Core\Application\Common\Session\Repository\DbReadSessionRepositoryInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($interfaceImplementationName);

    $this->identifierNodeInstance->name = 'FindHost';

    $rule = new RepositoryNameValidationByInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});