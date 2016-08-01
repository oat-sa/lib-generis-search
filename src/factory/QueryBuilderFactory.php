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

/**
 * Description of QueryBuilderFactory
 *
 * @author christophe
 */
class QueryBuilderFactory extends FactoryAbstract
{
    /**
     * supported interface 
     * @var string
     */
    protected $validInterface = 'oat\\search\\base\\QueryBuilderInterface';
    /**
     * return a new Query builder
     * @param string $className
     * @param array $options
     * @return \oat\search\factory\QueryInterface
     * @throws \InvalidArgumentException
     */
    public function get($className , array $options = array())  {
      
        $Query = $this->getServiceLocator()->get($className);
        if($this->isValidClass($Query)) {
            $Query->setServiceLocator($this->getServiceLocator())->setOptions($options);
            return $Query;
        }
    }
}
