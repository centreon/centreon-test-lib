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
use Centreon\PHPStan\CustomRules\StringBackquotesCustomRule;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

beforeEach(function () {
    $this->instanceNodeString = $this->createMock(String_::class);
    $this->scope = $this->createMock(Scope::class);
});

it('should return an error if :db is enclosed in backquotes and :dbstg is not.', function () {
    $this->scannedStringWithError =
        'INNER JOIN :dbstg.`centreon_acl` acl
            ON acl.host_id = ack.host_id
        INNER JOIN :`db`.`acl_groups` acg
            ON acg.acl_group_id = acl.group_id
            AND acg.acl_group_activate = \'1\'';

    $this->instanceNodeString->value = $this->scannedStringWithError;

    $expectedResult = [
        RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                StringBackquotesCustomRule::CENTREON_REALTIME_DATABASE,
                'must be enclosed in backquotes.'
            )
        )->build(),
    ];

    $rule = new StringBackquotesCustomRule();
    $result = $rule->processNode($this->instanceNodeString, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return an error if :dbstg is enclosed in backquotes and :db is not.', function () {
    $this->scannedStringWithError =
        'INNER JOIN `:dbstg`.`centreon_acl` acl
            ON acl.host_id = ack.host_id
        INNER JOIN :db.`acl_groups` acg
            ON acg.acl_group_id = acl.group_id
            AND acg.acl_group_activate = \'1\'';

    $this->instanceNodeString->value = $this->scannedStringWithError;

    $expectedResult = [
        RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                StringBackquotesCustomRule::CENTREON_CONFIG_DATABASE,
                'must be enclosed in backquotes.'
            )
        )->build(),
    ];

    $rule = new StringBackquotesCustomRule();
    $result = $rule->processNode($this->instanceNodeString, $this->scope);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
});

it('should return two errors if both :db and :dbstg is not enclosed in backquotes.', function () {
    $this->scannedStringWithError =
        'INNER JOIN :dbstg.`centreon_acl` acl
            ON acl.host_id = ack.host_id
        INNER JOIN :db.`acl_groups` acg
            ON acg.acl_group_id = acl.group_id
            AND acg.acl_group_activate = \'1\'';

    $this->instanceNodeString->value = $this->scannedStringWithError;

    $expectedResult = [
        RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                StringBackquotesCustomRule::CENTREON_REALTIME_DATABASE,
                'must be enclosed in backquotes.'
            )
        )->build(),
        RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                StringBackquotesCustomRule::CENTREON_CONFIG_DATABASE,
                'must be enclosed in backquotes.'
            )
        )->build(),
    ];

    $rule = new StringBackquotesCustomRule();
    $result = $rule->processNode($this->instanceNodeString, $this->scope);
    expect($result)->toHaveLength(2);
    expect($result[0]->message)->toBe($expectedResult[0]->message);
    expect($result[1]->message)->toBe($expectedResult[1]->message);
});

it('should not return an error if both :db and :dbstg is enclosed in backquotes.', function () {
    $this->scannedStringWithoutError =
        'INNER JOIN `:dbstg`.`centreon_acl` acl
            ON acl.host_id = ack.host_id
        INNER JOIN `:db`.`acl_groups` acg
            ON acg.acl_group_id = acl.group_id
            AND acg.acl_group_activate = \'1\'';

    $this->instanceNodeString->value = $this->scannedStringWithoutError;

    $rule = new StringBackquotesCustomRule();
    $result = $rule->processNode($this->instanceNodeString, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('should not return an error if a scanned string does not contain neither :db nor :dbstg.', function () {
    $this->scannedStringNeutral = 'SELECT * FROM `topology` WHERE `topology_page` = :id';

    $this->instanceNodeString->value = $this->scannedStringNeutral;

    $rule = new StringBackquotesCustomRule();
    $result = $rule->processNode($this->instanceNodeString, $this->scope);
    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
