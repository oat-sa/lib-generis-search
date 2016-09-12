<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\search\factory;

use \Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * search query basic factory
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class FactoryAbstract implements FactoryInterface {
    
    use ServiceLocatorAwareTrait;
    
    /**
     * interface which one all classes must implements
     * @var string
     */
    protected $validInterface;
    
    /**
     * verify if class implements valid interface
     * @param Object $object
     * @throws \InvalidArgumentException
     * @return boolean
     */
    protected function isValidClass($object) {
            
        if(is_a($object, $this->validInterface)) {    
            return true;
        }
        throw new \InvalidArgumentException(get_class($object) . ' doesn\'t implements ' . $this->validInterface  );
    }
    
}
