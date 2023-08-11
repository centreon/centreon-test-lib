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
use Centreon\PHPStan\CustomRules\RepositoryRules\RepositoryNameCustomRule;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

beforeEach(function (): void {
    $this->node = $this->createMock(Class_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->identifierNodeInstance = $this->createMock(Identifier::class);
    $this->nameNodeInstanceForNamespacedName = $this->createMock(Name::class);
    $this->node->name = $this->identifierNodeInstance;
    $this->node->namespacedName = $this->nameNodeInstanceForNamespacedName;
});

it('should return an error if Repository name does not correspont to naming requirement.', function (): void {
    $this->identifierNodeInstance->name = 'DbHostIdRepository';

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Repository name must start with data storage prefix(i.e. \'Db\', \'Redis\', etc.),'
            . ' followed by \'Read\' or \'Write\' and context mention.'
        )->build(),
    ];

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\RealTime\Repository\HostGroup\DbHostIdRepository');

    $rule = new RepositoryNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if Repository name does correspond to naming requirement.', function (): void {
    $this->identifierNodeInstance->name = 'DbReadSessionRepository';

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\Common\Repository\DbReadSessionRepository');

    $rule = new RepositoryNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned class is not a Repository.', function (): void {
    $this->identifierNodeInstance->name = 'SomeClass';

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Namespaced\Name\Of\SomeClassName');

    $rule = new RepositoryNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Repository is an Exception.', function (): void {
    $this->identifierNodeInstance->name = 'RepositoryException';

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\Common\Repository\RepositoryException');

    $rule = new RepositoryNameCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
