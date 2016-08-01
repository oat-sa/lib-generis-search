<?php

/*
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *  
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\search\test;

/**
 * Description of UnitTestHelper
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class UnitTestHelper extends \PHPUnit_Framework_TestCase 
{

    protected $instance;
    
    protected function getInaccessibleProperty($object , $propertyName) {
        $property = new \ReflectionProperty(get_class($object) , $propertyName);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible(false);
        return $value;
    }
    
    protected function setInaccessibleProperty($object , $propertyName, $value) {
        $property = new \ReflectionProperty(get_class($object) , $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
        return $this;
    }
    
    protected function callInaccessibleMethod($methodName , array $arguments = []) {
        $method = new \ReflectionMethod(get_class($this->instance) , $methodName);
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->instance, $arguments);
        $method->setAccessible(false);
        return $result;
    }
    
    /**
     * Call protected/private method of a class.
     *
     * @param object $object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeProtectedMethod($object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
    
}
