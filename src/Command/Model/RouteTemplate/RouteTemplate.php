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

namespace Centreon\Command\Model\RouteTemplate;

use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Model\FileTemplate;

class RouteTemplate extends FileTemplate
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
    private function getMethodeName(string $choice) :string
    {
        if($choice === CreateCoreArchCommand::COMMAND_ADD)
        {
            return "POST";
        }elseif ($choice === CreateCoreArchCommand::COMMAND_DELETE)
        {
            return "DELETE";
        }elseif ($choice === CreateCoreArchCommand::COMMAND_UPDATE)
        {
            return "PUT";
        }elseif ($choice === CreateCoreArchCommand::COMMAND_FIND)
        {
            return "GET";
        }
        throw new \InvalidArgumentException("Type $choice unknown");

    }

    /**
     * @return string
     */
    public function generateModelContent(string $useCase): string
    {

return <<<EOF
            {$useCase}{$this->name}:
                methods: {$this->getMethodeName($useCase)}
                path:
                controller: '{$this->namespace}{$useCase}{$this->name}Controller'
                condition:

            EOF;
    }
}
