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

namespace Tests\PHPStan\CustomRules\ArchitectureRules;

use Centreon\PHPStan\CustomRules\ArchitectureRules\DomainCallNamespacesCustomRule;
use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;

beforeEach(function (): void {
    $this->scope = $this->createMock(Scope::class);
    $this->collectedDataNode = $this->createMock(CollectedDataNode::class);
});

it('should return an error if Domain class calls Application and Infrastructure layer namespaces.', function (): void {
    $file = 'Core' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR
        . 'User' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'NewUser.php';

    $useUse = [
        [8, 'Core\\Application\\Configuration\\User\\UseCase\\FindUsers\\FindUsers'],
        [9, 'Core\\Infrastructure\\Configuration\\User\\Model\\DbReadUserRepository'],
    ];

    $useUseData = [$file => $useUse];

    $this->collectedDataNode
        ->expects($this->any())
        ->method('get')
        ->willReturn($useUseData);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Domain must not call Application or Infrastructure namespaces.'
        )->line($useUse[0][0])->file($file)->build(),
        CentreonRuleErrorBuilder::message(
            'Domain must not call Application or Infrastructure namespaces.'
        )->line($useUse[1][0])->file($file)->build(),
    ];

    $rule = new DomainCallNamespacesCustomRule();
    $result = $rule->processNode($this->collectedDataNode, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
    expect($result[1]->message)->toBe($expectedResult[1]->message);
});

it(
    'should return no error if Domain class does not call Application and Infrastructure layer namespaces.',
    function (): void {
        $file = 'Core' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR
            . 'User' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'NewUser.php';

        $useUse = [
            [8, 'Core\\Domain\\Traits\\LoggerTrait'],
            [9, 'Core\\Domain\\Configuration\\User\\Model\\User'],
        ];

        $useUseData = [$file => $useUse];

        $this->collectedDataNode
            ->expects($this->any())
            ->method('get')
            ->willReturn($useUseData);

        $rule = new DomainCallNamespacesCustomRule();
        $result = $rule->processNode($this->collectedDataNode, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);

it(
    'should return no error if scanned class is not in Domain and it calls Application and Infrastructure namespaces.',
    function (): void {
        $file = 'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Configuration'
            . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'UserException';

        $useUse = [
            [8, 'Core\\Application\\Configuration\\User\\UseCase\\FindUsers\\FindUsers'],
            [9, 'Core\\Infrastructure\\Configuration\\User\\Model\\DbReadUserRepository'],
        ];

        $useUseData = [$file => $useUse];

        $this->collectedDataNode
            ->expects($this->any())
            ->method('get')
            ->willReturn($useUseData);

        $rule = new DomainCallNamespacesCustomRule();
        $result = $rule->processNode($this->collectedDataNode, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);
