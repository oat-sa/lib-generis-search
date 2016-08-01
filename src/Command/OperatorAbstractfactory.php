<?php

/**
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

namespace oat\search\Command;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * use to create new Operators
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class OperatorAbstractfactory implements AbstractFactoryInterface {
    
    /**
     * create new oprator
     * @param ServiceLocatorInterface $serviceLocator
     * @param type $name
     * @param type $requestedName
     * @return object
     */
    public function createServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName) {

        return new $requestedName();
    }
    /**
     * verify if class name is an existing operator
     * @param ServiceLocatorInterface $serviceLocator
     * @param type $name
     * @param type $requestedName
     * @return boolean
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) {
        $interface = 'oat\search\base\command\OperatorConverterInterface';
        return (class_exists($requestedName) && in_array($interface , class_implements($requestedName)));
    }

}
