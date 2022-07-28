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

use Centreon\Domain\Log\LoggerTrait;
use Centreon\PHPStan\CustomRules\CustomRuleErrorMessage;
use Centreon\PHPStan\CustomRules\LogMethodInCatchCustomRule;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

beforeEach(function () {
    $this->scope = $this->createMock(Scope::class);
    $this->instanceCatchNode = $this->createMock(Catch_::class);
});

// it('should return an error if no Logger trait method is used in catch block.', function () {

//     // get test logic

//     $expectedResult = [
//         RuleErrorBuilder::message(
//             CustomRuleErrorMessage::buildErrorMessage(
//                 'Catch block must contain a Logger trait method call.'
//             )
//         )->build(),
//     ];

//     $rule = new LogMethodInCatchCustomRule();
//     $result = $rule->processNode($this->instanceCatchNode, $this->scope);
//     expect($result[0]->message)->toBe($expectedResult[0]->message);
// });

// it('should not return an error if one or more Logger trait methods are called in catch block.', function () {

//     // get test logic

//     $rule = new LogMethodInCatchCustomRule();
//     $result = $rule->processNode($this->instanceCatchNode, $this->scope);
//     expect($result)->toBeArray();
//     expect($result)->toBeEmpty();
// });

it('should not return an error if scanned node does not refer to catch block', function () {
    $this->instanceIfNode = $this->createMock(If_::class);
    $rule = new LogMethodInCatchCustomRule();
    $result = $rule->processNode($this->instanceIfNode, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});