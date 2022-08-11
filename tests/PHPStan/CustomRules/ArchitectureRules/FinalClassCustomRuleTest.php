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

namespace Tests\PHPStan\CustomRules\ArchitectureRules;

use Centreon\PHPStan\CustomRules\ArchitectureRules\FinalClassCustomRule;
use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

beforeEach(function () {
    $this->node = $this->createMock(Class_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->instanceIdentifierNode = $this->createMock(Identifier::class);
    $this->node->name = $this->instanceIdentifierNode;
});

it('should return an error if UseCase class in not final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatus';
    $this->scope
        ->expects($this->any())
        ->method('getFile')
        ->willReturn(
            DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
        );

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(false);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Class ' . $this->node->name->name . ' must be final.'
        )->build(),
    ];

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if Request class is not final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusRequest';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(false);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Class ' . $this->node->name->name . ' must be final.'
        )->build(),
    ];

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if Response class is not final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusResponse';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(false);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Class ' . $this->node->name->name . ' must be final.'
        )->build(),
    ];

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if Controller class is not final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusController';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(false);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            'Class ' . $this->node->name->name . ' must be final.'
        )->build(),
    ];

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return no error if UseCase class is final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatus';
    $this->scope
        ->expects($this->any())
        ->method('getFile')
        ->willReturn(
            DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
        );

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(true);

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Request class is final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusRequest';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(true);

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Response class is final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusResponse';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(true);

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should return no error if Controller class is final.', function () {
    $this->instanceIdentifierNode->name = 'FindInstallationStatusController';

    $this->node
        ->expects($this->any())
        ->method('isFinal')
        ->willReturn(true);

    $rule = new FinalClassCustomRule();
    $result = $rule->processNode($this->node, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it(
    'should return no error if scanned class is not final and is neither UseCase, Controller, Response no Request',
    function () {
        $this->instanceIdentifierNode->name = 'User';

        $this->scope
        ->expects($this->any())
        ->method('getFile')
        ->willReturn(
            DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR . 'User'
                . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'User.php'
        );

        $this->node
            ->expects($this->any())
            ->method('isFinal')
            ->willReturn(false);

        $rule = new FinalClassCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);
