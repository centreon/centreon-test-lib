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

namespace Centreon\Command\Model\PresenterTemplate;

use Centreon\Command\Model\FileTemplate;

class PresenterInterfaceTemplate extends FileTemplate implements \Stringable
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param boolean $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use Core\Application\Common\UseCase\PresenterInterface;

        interface $this->name extends PresenterInterface
        {
        }

        EOF;

        return $content;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
