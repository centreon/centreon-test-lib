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

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\VariableLengthCustomRule;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassPropertyNode;

beforeEach(function () {
    $this->nodeInstanceClassProperty = $this->createMock(ClassPropertyNode::class);
    $this->nodeInstancePropertyFetch = $this->createMock(PropertyFetch::class);
    $this->nodeInstancePropertyFetchName = $this->createMock(Expr::class);
    $this->nodeInstanceVariable = $this->createMock(Variable::class);
    $this->nodeInstanceParam = $this->createMock(Param::class);
    $this->nodeInstanceParam->var = $this->nodeInstanceVariable;
    $this->nodeInstancePropertyFetch->name = $this->nodeInstancePropertyFetchName;
    $this->nodeInstanceIfKeyword = $this->createMock(If_::class);
    $this->scope = $this->createMock(Scope::class);
    $this->invalidVariableName = 'sv';
});

it('should return an error if the object property length is less than 3 characters.', function () {
    $this->nodeInstanceClassProperty
        ->expects($this->any())
        ->method('getType')
        ->willReturn('PHPStan_Node_ClassPropertyNode');
    $this->nodeInstanceClassProperty
        ->expects($this->any())
        ->method('getName')
        ->willReturn($this->invalidVariableName);

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            "$$this->invalidVariableName must contain 3 or more characters."
        )->build(),
    ];

    $rule = new VariableLengthCustomRule();
    $resultClassProperty = $rule->processNode($this->nodeInstanceClassProperty, $this->scope);
    expect($resultClassProperty[0]->message)->toBe($expectedResult[0]->message);
});

/**
 * Fetched Property are properties like : $this->myVar
 */
it('should return an error if the object fetched property length is less than 3 characters.', function () {
    $this->nodeInstancePropertyFetch
        ->expects($this->any())
        ->method('getType')
        ->willReturn('Expr_PropertyFetch');
    $this->nodeInstancePropertyFetch->name->name = $this->invalidVariableName;

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            "$$this->invalidVariableName must contain 3 or more characters."
        )->build(),
    ];

    $rule = new VariableLengthCustomRule();
    $resultPropertyFetch = $rule->processNode($this->nodeInstancePropertyFetch, $this->scope);
    expect($resultPropertyFetch[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if a variable length is less than 3 characters.', function () {
    $this->nodeInstanceVariable
        ->expects($this->any())
        ->method('getType')
        ->willReturn('Expr_Variable');
    $this->nodeInstanceVariable->name = $this->invalidVariableName;

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            "$$this->invalidVariableName must contain 3 or more characters."
        )->build(),
    ];

    $rule = new VariableLengthCustomRule();
    $resultVariable = $rule->processNode($this->nodeInstanceVariable, $this->scope);
    expect($resultVariable[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if a method parameter length is less than 3 characters.', function () {
    $this->nodeInstanceParam
        ->expects($this->any())
        ->method('getType')
        ->willReturn('Param');
    $this->nodeInstanceParam->var->name = $this->invalidVariableName;

    $expectedResult = [
        CentreonRuleErrorBuilder::message(
            "$$this->invalidVariableName must contain 3 or more characters."
        )->build(),
    ];

    $rule = new VariableLengthCustomRule();
    $resultParam = $rule->processNode($this->nodeInstanceParam, $this->scope);
    expect($resultParam[0]->message)->toBe($expectedResult[0]->message);
});

it("should return no error if the variable name is 'ex'.", function () {
    $variableName = 'ex';
    $this->nodeInstanceClassProperty
        ->expects($this->any())
        ->method('getType')
        ->willReturn('PHPStan_Node_ClassPropertyNode');

    $this->nodeInstanceClassProperty
        ->expects($this->any())
        ->method('getName')
        ->willReturn($variableName);

    $rule = new VariableLengthCustomRule();
    $result = $rule->processNode($this->nodeInstanceClassProperty, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it("should return no error if scanned node doesn't refer to a property/variable/parameter", function () {
    $this->nodeInstanceIfKeyword
        ->expects($this->any())
        ->method('getType')
        ->willReturn('Stmt_If');

    $rule = new VariableLengthCustomRule();
    $result = $rule->processNode($this->nodeInstanceIfKeyword, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
