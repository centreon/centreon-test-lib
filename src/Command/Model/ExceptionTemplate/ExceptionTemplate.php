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
    public static function getActionName(string $choice) :string
    {

        if ($choice === CreateCoreArchCommand::COMMAND_ADD) {
            return "Ading";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_DELETE){
            return "Deleting";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_UPDATE) {
            return "Updating";
        } elseif ($choice === CreateCoreArchCommand::COMMAND_FIND) {
            return "Finding";
        }
        throw new \InvalidArgumentException("UseCase $choice unknown");
    }
    public static function getMethodeName(string $useCase): string
    {
        return "errorWhile" . self::getActionName($useCase);
    }
    public function verifErrorWhile(string $useCaseType): string
    {
        $methodeName = $this->getMethodeName($useCaseType);
        $lowCaseActionName = lcfirst(self::getActionName($useCaseType));
            return <<<ADDING
                public static function $methodeName(): self
                    {
                        return new self(_('Error while $lowCaseActionName a {$this->name}'));
                    }
                ADDING;
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
