<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Centreon\Test\Mock\Object;


class BaseObject
{
    protected $incrementalId;
    
    public function __construct($fakeDb)
    {
        $this->incrementalId = 1;
    }
    
    protected function getIncrementedId()
    {
        return $this->incrementalId++;
    }
}
