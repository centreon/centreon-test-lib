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

namespace Tests\PHPStan\CustomRules;

use Centreon\PHPStan\CustomRules\CustomRuleErrorMessage;
use Centreon\PHPStan\CustomRules\LogMethodInCatchCustomRule;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

beforeEach(function () {
    $this->scope = $this->createMock(Scope::class);
    $this->instanceCatchNode = $this->createMock(Catch_::class);
    $this->instanceMethodCallNodeOne = $this->createMock(MethodCall::class);
    $this->instanceMethodCallNodeTwo = $this->createMock(MethodCall::class);
    $this->instanceExpressionNodeOne = $this->createMock(Expr::class);
    $this->instanceExpressionNodeTwo = $this->createMock(Expr::class);
    $this->instanceStatementNodeOne = $this->createMock(Stmt::class);
    $this->instanceStatementNodeTwo = $this->createMock(Stmt::class);
});

it('should return an error if no Logger trait method is used in catch block.', function () {

    $methodCallOne = 'methodOne';
    $methodCallTwo = 'methodTwo';

    $this->instanceMethodCallNodeOne->name = $methodCallOne;
    $this->instanceMethodCallNodeTwo->name = $methodCallTwo;

    $this->instanceExpressionNodeOne->name = $this->instanceMethodCallNodeOne;
    $this->instanceExpressionNodeTwo->name = $this->instanceMethodCallNodeTwo;

    $this->instanceStatementNodeOne->expr = $this->instanceExpressionNodeOne;
    $this->instanceStatementNodeTwo->expr = $this->instanceExpressionNodeTwo;

    $this->instanceCatchNode->stmts = [
        $this->instanceStatementNodeOne,
        $this->instanceStatementNodeTwo
    ];

    $expectedResult = [
        RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                'Catch block must contain a Logger trait method call.'
            )
        )->build(),
    ];

    $rule = new LogMethodInCatchCustomRule();
    $result = $rule->processNode($this->instanceCatchNode, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should not return an error if one or more Logger trait methods are called in catch block.', function () {

    $methodCallOne = 'methodOne';
    $methodCallTwo = 'log';

    $this->instanceMethodCallNodeOne->name = $methodCallOne;
    $this->instanceMethodCallNodeTwo->name = $methodCallTwo;

    $this->instanceExpressionNodeOne->name = $this->instanceMethodCallNodeOne;
    $this->instanceExpressionNodeTwo->name = $this->instanceMethodCallNodeTwo;

    $this->instanceStatementNodeOne->expr = $this->instanceExpressionNodeOne;
    $this->instanceStatementNodeTwo->expr = $this->instanceExpressionNodeTwo;

    $this->instanceCatchNode->stmts = [
        $this->instanceStatementNodeOne,
        $this->instanceStatementNodeTwo
    ];

    $rule = new LogMethodInCatchCustomRule();
    $result = $rule->processNode($this->instanceCatchNode, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
