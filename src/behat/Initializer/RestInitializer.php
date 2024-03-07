<?php
/**
 * Copyright 2016 Centreon
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
 */
namespace Centreon\Test\Behat\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Centreon\Test\Behat\RestContext;

/**
 * Rest initializer
 */
class RestInitializer implements ContextInitializer
{
    private $parameters;
    
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }
    
    public function initializeContext(Context $context): void
    {
        if (!$context instanceof RestContext) {
            return;
        }
        $context->setRestParameters($this->parameters);
    }
}
