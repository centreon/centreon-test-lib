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
namespace Centreon\Test\Mock\Object;

class CentreonHost extends BaseObject
{
    private static $countHostFunction = 0;

    public function getHostId($host_name)
    {
        return $this->getIncrementedId();
    }

    public function update($hostId, $hostProperties)
    {

    }

    public function insert($hostProperties)
    {
        return $this->getIncrementedId();
    }

    public function insertMacro($hostId, $macroInput, $macroValue, $macroPassword, $macroDescription)
    {

    }

    public function deleteHostByName($host_name)
    {

    }

    public function setTemplates($hostId, $hostTemplates)
    {

    }

    public function insertRelHostService()
    {

    }

    public function getHostByAddress($host_ip, $filter)
    {
        self::$countHostFunction++;
        if (self::$countHostFunction <= 2) {
            return array(
                array(
                    "host_id" => self::$countHostFunction,
                    "host_name" => "10.30.2." . self::$countHostFunction
                )
            );
        } else {
            return array();
        }
    }

    public function getServices($hostId)
    {
        return array("180" => "Ping");
    }

}
