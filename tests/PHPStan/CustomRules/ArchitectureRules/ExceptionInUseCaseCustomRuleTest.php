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

use Centreon\PHPStan\CustomRules\ArchitectureRules\ExceptionInUseCaseCustomRule;
use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\Analyser\Scope;

beforeEach(function (): void {
    $this->node = $this->createMock(Throw_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->instanceIf_Node = $this->createMock(If_::class);
    $this->instanceClassMethodNode = $this->createMock(ClassMethod::class);
    $this->instanceNew_Node = $this->createMock(New_::class);
    $this->instanceNameNode = $this->createMock(Name::class);
    $this->instanceTryCatchNode = $this->createMock(TryCatch::class);
    $this->instanceCatch_Node01 = $this->createMock(Catch_::class);
    $this->instanceCatch_Node02 = $this->createMock(Catch_::class);
    $this->instanceNameNode01 = $this->createMock(Name::class);
    $this->instanceNameNode02 = $this->createMock(Name::class);
    $this->instanceNameNode03 = $this->createMock(Name::class);
    $this->instanceNameNode04 = $this->createMock(Name::class);
    $this->node->expr = $this->instanceNew_Node;
    $this->instanceNew_Node->class = $this->instanceNameNode;
    $this->instanceClass_Node = $this->createMock(Class_::class);
    $this->instanceIdentifierNode = $this->createMock(Identifier::class);
    $this->instanceClassMethodNode->name = $this->instanceIdentifierNode;
    $this->instanceClassMethodNode
        ->expects($this->any())
        ->method('getAttribute')
        ->with($this->equalTo('parent'))
        ->willReturn($this->instanceClass_Node);
});

it(
    'should return an error if scanned class is UseCase and an Exception is thrown outside of try/catch block.',
    function (): void {

        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PDOException');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceIf_Node);

        $this->instanceIf_Node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $expectedResult = [
            CentreonRuleErrorBuilder::message(
                'Exception thrown in UseCase should be in a try catch block, and must be caught.'
            )->build(),
        ];

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result[0]->message)->toBe($expectedResult[0]->message);
    }
);

it(
    'should return an error if scanned class is UseCase and an Exception is thrown inside try/catch (with multiple '
    . 'catches) block, but it is not caught by either catch.',
    function (): void {
        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\Exception');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceTryCatchNode);

        $this->instanceTryCatchNode
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $this->instanceTryCatchNode->catches = [
            $this->instanceCatch_Node01,
            $this->instanceCatch_Node02,
        ];

        $this->instanceCatch_Node01->types = [
            $this->instanceNameNode01,
            $this->instanceNameNode02,
        ];

        $this->instanceCatch_Node02->types = [
            $this->instanceNameNode03,
            $this->instanceNameNode04,
        ];

        $this->instanceNameNode01
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\DOMException');

        $this->instanceNameNode02
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\IntlException');

        $this->instanceNameNode03
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\LogicException');

        $this->instanceNameNode04
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PharException');

        $expectedResult = [
            CentreonRuleErrorBuilder::message(
                'Exception thrown in UseCase should be in a try catch block, and must be caught.'
            )->build(),
        ];

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result[0]->message)->toBe($expectedResult[0]->message);
    }
);

it(
    'should return no error if scanned class is UseCase, an Exception is thrown inside try/catch (with multiple '
    . 'catches) block and it is caught by one of the catches.',
    function (): void {
        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\Exception');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceTryCatchNode);

        $this->instanceTryCatchNode
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $this->instanceTryCatchNode->catches = [
            $this->instanceCatch_Node01,
            $this->instanceCatch_Node02,
        ];

        $this->instanceCatch_Node01->types = [
            $this->instanceNameNode01,
            $this->instanceNameNode02,
        ];

        $this->instanceCatch_Node02->types = [
            $this->instanceNameNode03,
            $this->instanceNameNode04,
        ];

        $this->instanceNameNode01
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\DOMException');

        $this->instanceNameNode02
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\IntlException');

        $this->instanceNameNode03
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\LogicException');

        $this->instanceNameNode04
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\Exception');

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);

it(
    'should return no error if scanned class is UseCase and an Exception is thrown (and caught) inside try/catch block,'
    . 'but try/catch block is not a direct parent of a thrown Exception.',
    function (): void {
        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PDOException');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceIf_Node);

        $this->instanceIf_Node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceTryCatchNode);

        $this->instanceTryCatchNode
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $this->instanceTryCatchNode->catches = [
            $this->instanceCatch_Node01,
        ];

        $this->instanceCatch_Node01->types = [
            $this->instanceNameNode01,
            $this->instanceNameNode02,
        ];

        $this->instanceNameNode01
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PDOException');

        $this->instanceNameNode02
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\IntlException');

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);

it(
    'should return no error if scanned class is not UseCase and an Exception is thrown outside try/catch block.',
    function (): void {
        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Configuration' . DIRECTORY_SEPARATOR
                . 'User' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'NewUser.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PDOException');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceIf_Node);

        $this->instanceIf_Node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);

it(
    'should return no error if scanned class is UseCase and an Exception is thrown outside try/catch block in '
    . 'constructor method',
    function (): void {
        $this->scope
            ->expects($this->any())
            ->method('getFile')
            ->willReturn(
                'Core' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
                . 'FindInstallationStatus' . DIRECTORY_SEPARATOR . 'FindInstallationStatus.php'
            );

        $this->instanceNameNode
            ->expects($this->any())
            ->method('toCodeString')
            ->willReturn('\PDOException');

        $this->node
            ->expects($this->any())
            ->method('getAttribute')
            ->with($this->equalTo('parent'))
            ->willReturn($this->instanceClassMethodNode);

        $this->instanceIdentifierNode->name = '__construct';

        $rule = new ExceptionInUseCaseCustomRule();
        $result = $rule->processNode($this->node, $this->scope);
        expect($result)->toBeArray();
        expect($result)->toBeEmpty();
    }
);
