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
use Centreon\PHPStan\CustomRules\RepositoryRules\RepositoryImplementsInterfaceCustomRule;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

beforeEach(function () {
    $this->scope = $this->createMock(Scope::class);
    $this->node = $this->createMock(Class_::class);
    $this->identifierNodeInstance = $this->createMock(Identifier::class);
    $this->nameNodeInstanceInterface = $this->createMock(Name::class);
    $this->nameNodeInstanceForNamespacedName = $this->createMock(Name::class);
    $this->node->name = $this->identifierNodeInstance;
    $this->node->namespacedName = $this->nameNodeInstanceForNamespacedName;
    $this->node->implements = [
        $this->nameNodeInstanceInterface,
    ];
});

it('should return an error if Repository does not implement an Interface defined in Application layer.', function () {
    $invalidInterfaceImplementations = 'Core\Domain\Configuration\Notification\Model\NotificationInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($invalidInterfaceImplementations);

    $this->identifierNodeInstance->name = 'DbReadHostgroupRepository';

    $this->node
        ->expects($this->any())
        ->method('isAbstract')
        ->willReturn(false);

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\RealTime\Repository\HostGroup\DbReadHostGroupRepository');

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Repositories must implement an Interface defined in Application layer.'
        )->build(),
    ];

    $rule = new RepositoryImplementsInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if Repository does implement an Interface defined in Application layer.', function () {
    $validInterfaceImplementations = 'Core\Application\Common\Session\Repository\ReadSessionRepositoryInterface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($validInterfaceImplementations);

    $this->identifierNodeInstance->name = 'DbReadSessionRepository';

    $this->node
        ->expects($this->any())
        ->method('isAbstract')
        ->willReturn(false);

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\Common\Repository\DbReadSessionRepository');

    $rule = new RepositoryImplementsInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if scanned class is not a Repository.', function () {
    $invalidInterfaceImplementations = 'Path\To\Some\Interface';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($invalidInterfaceImplementations);

    $this->identifierNodeInstance->name = 'SomeClassName';

    $this->node
        ->expects($this->any())
        ->method('isAbstract')
        ->willReturn(false);

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Namespaced\Name\Of\SomeClassName');

    $rule = new RepositoryImplementsInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Repository is an abstract class.', function () {
    $invalidInterfaceImplementations = '';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($invalidInterfaceImplementations);

    $this->identifierNodeInstance->name = 'AbstractDbReadNotificationRepository';

    $this->node
        ->expects($this->any())
        ->method('isAbstract')
        ->willReturn(true);

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn(
            'Core\Infrastructure\Configuration\NotificationPolicy\Repository\AbstractDbReadNotificationRepository'
        );

    $rule = new RepositoryImplementsInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Repository is an Exception.', function () {
    $invalidInterfaceImplementations = '';

    $this->nameNodeInstanceInterface
        ->expects($this->any())
        ->method('toString')
        ->willReturn($invalidInterfaceImplementations);

    $this->identifierNodeInstance->name = 'RepositoryException';

    $this->node
        ->expects($this->any())
        ->method('isAbstract')
        ->willReturn(false);

    $this->nameNodeInstanceForNamespacedName
        ->expects($this->any())
        ->method('toCodeString')
        ->willReturn('Core\Infrastructure\Common\Repository\RepositoryException');

    $rule = new RepositoryImplementsInterfaceCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
