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

namespace Centreon\Command\Model\FactoryTemplate;

use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;

class FactoryTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param ModelTemplate $modelTemplate
     * @param bool $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public ModelTemplate $modelTemplate,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        if ($this->modelTemplate->isNewFlag === true) {
            $modelName = 'New' . $this->modelTemplate->name;
        } else {
            $modelName = $this->modelTemplate->name;
        }
        $modelNamespace = $this->modelTemplate->namespace . '\\' . $modelName;

        return <<<EOF
            <?php
            {$this->licenceHeader}
            declare(strict_types=1);

            namespace {$this->namespace};

            use {$modelNamespace};

            class {$this->name}
            {
                public static function create(): {$modelName}
                {
                    return new {$modelName}();
                }
            }

            EOF;
    }
}
