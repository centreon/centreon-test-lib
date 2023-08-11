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
use Centreon\PHPStan\CustomRules\LoggerRules\LogMethodInCatchCustomRule;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;

beforeEach(function (): void {
    $this->scope = $this->createMock(Scope::class);
    $this->instanceCatchNode = $this->createMock(Catch_::class);
    $this->methodCallNodeInstanceOne = $this->createMock(MethodCall::class);
    $this->methodCallNodeInstanceTwo = $this->createMock(MethodCall::class);
    $this->expressionNodeInstanceOne = $this->createMock(Expr::class);
    $this->expressionNodeInstanceTwo = $this->createMock(Expr::class);
    $this->statementNodeInstanceOne = $this->createMock(Stmt::class);
    $this->statementNodeInstanceTwo = $this->createMock(Stmt::class);
    $this->expressionNodeInstanceOne->name = $this->methodCallNodeInstanceOne;
    $this->expressionNodeInstanceTwo->name = $this->methodCallNodeInstanceTwo;
    $this->statementNodeInstanceOne->expr = $this->expressionNodeInstanceOne;
    $this->statementNodeInstanceTwo->expr = $this->expressionNodeInstanceTwo;
    $this->instanceCatchNode->stmts = [
        $this->statementNodeInstanceOne,
        $this->statementNodeInstanceTwo,
    ];
});

it('should return an error if no Logger trait method is used in catch block.', function (): void {
    $this->methodCallNodeInstanceOne->name = 'methodOne';
    $this->methodCallNodeInstanceTwo->name = 'methodTwo';

    $expectedResult = [
        CentreonRuleErrorBuilder::message('Catch block must contain a Logger trait method call.')->build(),
    ];

    $rule = new LogMethodInCatchCustomRule();
    $result = $rule->processNode($this->instanceCatchNode, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if one or more Logger trait methods are called in catch block.', function (): void {
    $this->methodCallNodeInstanceOne->name = 'methodOne';
    $this->methodCallNodeInstanceTwo->name = 'log';

    $rule = new LogMethodInCatchCustomRule();
    $result = $rule->processNode($this->instanceCatchNode, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
