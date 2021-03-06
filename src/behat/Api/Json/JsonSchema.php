<?php

/*
 * Copyright 2005 - 2020 Centreon (https://www.centreon.com/)
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
 *
 */

namespace Centreon\Test\Behat\Api\Json;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

class JsonSchema extends Json
{
    /**
     * @var string|null
     */
    private $uri;

    public function __construct($content, $uri = null)
    {
        $this->uri = $uri;

        parent::__construct($content);
    }

    /**
     * Resolve json ref
     *
     * @param SchemaStorage $resolver
     * @return JsonSchema
     */
    public function resolve(SchemaStorage $resolver)
    {
        if (!$this->hasUri()) {
            return $this;
        }

        $this->content = $resolver->resolveRef($this->uri);

        return $this;
    }

    /**
     * Validate json schema
     *
     * @param Json $json
     * @param Validator $validator
     * @return boolean
     */
    public function validate(Json $json, Validator $validator)
    {
        $jsonContent = $json->getContent();
        $validator->validate($jsonContent, $this->getContent());

        if (!$validator->isValid()) {
            $msg = "JSON does not validate. Violations:" . PHP_EOL;
            foreach ($validator->getErrors() as $error) {
                $msg .= sprintf("  - [%s] %s".PHP_EOL, $error['property'], $error['message']);
            }
            throw new \Exception($msg);
        }

        return true;
    }

    /**
     * Check if uri is defined
     *
     * @return boolean
     */
    private function hasUri()
    {
        return null !== $this->uri;
    }
}
