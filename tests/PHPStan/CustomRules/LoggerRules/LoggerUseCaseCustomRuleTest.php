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

namespace Tests\PHPStan\CustomRules\LoggerRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\LoggerRules\LoggerUseCaseCustomRule;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;

beforeEach(function (): void {
    $this->scope = $this->createMock(Scope::class);
    $this->collectedDataNode = $this->createMock(CollectedDataNode::class);
});

it('should return an error if a Use Case does not contain a call to Logger method.', function (): void {
    $file = 'centreon' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
        . 'Application' . DIRECTORY_SEPARATOR . 'RealTime' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
        . 'FindHost' . DIRECTORY_SEPARATOR . 'FindHost.php';
    $methodCalls = [
        'methodOne',
        'methodTwo',
        'methodThree',
    ];

    $methodCallData = [$file => $methodCalls];
    $this->collectedDataNode
        ->expects($this->any())
        ->method('get')
        ->willReturn($methodCallData);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Class must contain a Logger trait and call at least one of its methods.'
        )->build(),
    ];

    $rule = new LoggerUseCaseCustomRule();
    $result = $rule->processNode($this->collectedDataNode, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if a Use Case contain a call to Logger method.', function (): void {
    $file = 'centreon' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
        . 'Application' . DIRECTORY_SEPARATOR . 'RealTime' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
        . 'FindHost' . DIRECTORY_SEPARATOR . 'FindHost.php';
    $methodCalls = [
        'methodOne',
        'methodTwo',
        'methodThree',
        'critical',
    ];

    $methodCallData = [$file => $methodCalls];
    $this->collectedDataNode
        ->expects($this->any())
        ->method('get')
        ->willReturn($methodCallData);

    $rule = new LoggerUseCaseCustomRule();
    $result = $rule->processNode($this->collectedDataNode, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it(
    'should not return an error if scanned file is not a Use case and does not contain a call to Logger method.',
    function (): void {
        $file = 'centreon' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . 'Application' . DIRECTORY_SEPARATOR . 'RealTime' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
            . 'FindHost' . DIRECTORY_SEPARATOR . 'FindHostResponse.php';
        $methodCalls = [
            'methodOne',
            'methodTwo',
            'methodThree',
        ];

        $methodCallData = [$file => $methodCalls];
        $this->collectedDataNode
            ->expects($this->any())
            ->method('get')
            ->willReturn($methodCallData);

        $rule = new LoggerUseCaseCustomRule();
        $result = $rule->processNode($this->collectedDataNode, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);
