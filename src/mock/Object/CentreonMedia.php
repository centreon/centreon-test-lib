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

class CentreonMedia extends BaseObject
{
    public function getMediaDirectory()
    {
        return '/tmp/';
    }
    
    public function getDirectoryId($dirname)
    {
        return $this->incrementalId++;
    }
    
    public function getDirectoryName($directoryId)
    {
        return '/tmp/';
    }
    
    public function addDirectory($dirname, $dirAlias = null)
    {
        return $this->incrementalId++;
    }
    
    public function getImageId($imagename, $dirname = null)
    {
        return $this->incrementalId++;
    }
    
    public function getFilename($imgId = null)
    {
        return '/tmp/myIcon.png';
    }
    
    public function addImage($parameters, $binary = null)
    {
        return $this->incrementalId++;
    }
}