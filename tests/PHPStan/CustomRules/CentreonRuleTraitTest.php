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

namespace Tests\PHPStan\CustomRules\RepositoryRules;

use Centreon\PHPStan\CustomRules\CentreonRuleTrait;

beforeEach(
    function (): void {
        $this->trait = new class {
            use CentreonRuleTrait;

            public function testGetRepositoryName(...$args): mixed
            {
                return $this->getRepositoryName(...$args);
            }

            public function testGetRepositoryInterfaceName(...$args): mixed
            {
                return $this->getRepositoryInterfaceName(...$args);
            }

            public function testExtendsAnException(...$args): mixed
            {
                return $this->extendsAnException(...$args);
            }

            public function testFileIsUseCase(...$args): mixed
            {
                return $this->fileIsUseCase(...$args);
            }
        };
    }
);

it(
    'should return a correct bool for method fileIsUseCase()',
    function (bool $expected, mixed ...$args): void {
        expect($this->trait->testFileIsUseCase(...$args))->toBe($expected);
    }
)->with([
    [false, 'Foo/Bar/WithAFile.php'],
    [false, 'Foo\\Bar\\WithAFile.php'],
    [false, 'Foo/UseCase/WithAFile.php'],
    [false, 'Foo\\UseCase\\WithAFile.php'],
    [false, 'Foo/UseCaseBar/WithAFile/WithAFile.php'],
    [false, 'Foo\\UseCaseBar\\WithAFile\\WithAFile.php'],
    [true, 'Foo/UseCase/WithAFile/WithAFile.php'],
    [true, 'Foo\\UseCase\\WithAFile\\WithAFile.php'],
    [true, 'Foo/UseCase/Bar/WithAFile/WithAFile.php'],
    [true, 'Foo\\UseCase\\Bar\\WithAFile\\WithAFile.php'],
]);

it(
    'should return a correct bool for method extendsAnException()',
    function (bool $expected, mixed ...$args): void {
        expect($this->trait->testExtendsAnException(...$args))->toBe($expected);
    }
)->with([
    [false, null],
    [false, ''],
    [false, \TypeError::class],
    [true, \InvalidArgumentException::class],
    [true, \OutOfBoundsException::class],
]);

it(
    'should return a correct value for method getRepositoryName()',
    function (?string $expected, mixed ...$args): void {
        expect($this->trait->testGetRepositoryName(...$args))->toBe($expected);
    }
)->with([
    [null, 'Repository'],
    [null, 'abcRepository'],
    ['Abc', 'AbcRepository'],
    [null, 'AbcRepositorySuffix'],
    ['Def', 'Abc\DefRepository'],
    [null, 'Abc/DefRepository'],
]);

it(
    'should return a correct value for method getRepositoryInterfaceName()',
    function (?string $expected, mixed ...$args): void {
        expect($this->trait->testGetRepositoryInterfaceName(...$args))->toBe($expected);
    }
)->with([
    [null, 'RepositoryInterface'],
    [null, 'abcRepositoryInterface'],
    ['Abc', 'AbcRepositoryInterface'],
    [null, 'AbcRepositoryInterfaceSuffix'],
    ['Def', 'Abc\DefRepositoryInterface'],
    [null, 'Abc/DefRepositoryInterface'],
]);
