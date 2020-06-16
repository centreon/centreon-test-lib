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

use Symfony\Component\PropertyAccess\PropertyAccessor;

class Json
{
    /**
     * @var array
     */
    protected $content;

    public function __construct($content)
    {
        $this->content = $this->decode((string) $content);
    }

    /**
     * content getter
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * read json and get value corresponding to the accessor
     *
     * @param array|string $expression
     * @param PropertyAccessor $accessor
     * @return array|string
     */
    public function read($expression, PropertyAccessor $accessor)
    {
        if (is_array($this->content)) {
            $expression =  preg_replace('/^root/', '', $expression);
        } else {
            $expression =  preg_replace('/^root./', '', $expression);
        }

        // If root asked, we return the entire content
        if (strlen(trim($expression)) <= 0) {
            return $this->content;
        }

        return $accessor->getValue($this->content, $expression);
    }

    /**
     * Encode json
     *
     * @param boolean $pretty
     * @return string
     */
    public function encode($pretty = true)
    {
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        if (true === $pretty && defined('JSON_PRETTY_PRINT')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($this->content, $flags);
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->encode(false);
    }

    /**
     * Decode json
     *
     * @param string $content
     * @return array|string
     */
    private function decode($content)
    {
        $result = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("The string '$content' is not valid json");
        }

        return $result;
    }
}
