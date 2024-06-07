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

namespace Centreon\Command\Model\ExceptionTemplate;

use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Model\FileTemplate;


class ExceptionTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,

    ) {
        parent::__construct();

    }
    public function getName(): string
    {
        return $this->name;
    }
    public static function getMethodeName(string $choice) :string
    {
        if ($choice === CreateCoreArchCommand::COMMAND_ADD) {
            return "errorWhileAdding";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_DELETE){
            return "errorWhileDeleting";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_UPDATE) {
            return "errorWhileUpdating";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_FIND) {
            return "errorWhileFinding";
        }
        throw new \InvalidArgumentException("UseCase $choice unknown");

    }
    public function verifErrorWhile(string $choice): string
    {
        $methodeName = $this->getMethodeName($choice);
        if($choice === CreateCoreArchCommand::COMMAND_ADD) {
            return <<<ADDING
                public static function $methodeName(): self
                    {
                        return new self(_('Error while adding a {$this->name}'));
                    }
                ADDING;
        } elseif ($choice == CreateCoreArchCommand::COMMAND_DELETE) {
            return <<<DELETING
                public static function $methodeName(): self
                    {
                        return new self(_('Error while deleting a {$this->name}'));
                    }
                DELETING;
        } elseif ($choice == CreateCoreArchCommand::COMMAND_UPDATE) {
            return <<<UPDATING
                public static function $methodeName(): self
                    {
                        return new self(_('Error while updating a {$this->name}'));
                    }
                UPDATING;
        } elseif ($choice == CreateCoreArchCommand::COMMAND_FIND) {
            return <<<SEARCHING
                public static function $methodeName(): self
                    {
                        return new self(_('Error while searching a {$this->name}'));
                    }
                SEARCHING;
        }
        return $response = '';
    }

    /**
     * @return string
     */
    public function generateModelContent(string $useCase): string
    {
        $texte = $this->verifErrorWhile($useCase);

        return <<<EOF
            <?php
            {$this->licenceHeader}
            declare(strict_types=1);
            
            namespace {$this->namespace};

            class {$this->name}Exception extends \Exception
            {
                {$texte}
            }

            EOF;
    }
}
