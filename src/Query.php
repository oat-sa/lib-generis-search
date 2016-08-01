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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\search;

use oat\search\factory\FactoryAbstract;
use oat\search\factory\QueryParamFactory;
use oat\search\base\QueryInterface;
use oat\search\UsableTrait\OptionsTrait;
use oat\search\UsableTrait\ParentFluateTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * implemented query object
 */
class Query implements QueryInterface, ServiceLocatorAwareInterface {

    use OptionsTrait;
    use ServiceLocatorAwareTrait;
    use ParentFluateTrait;
    
    /**
     * stored conditions
     * @var array
     */
    protected $storedQueryParams = [];
    /**
     * default QueryParam factory
     * @var \oat\search\factory\FactoryInterface
     */
    protected $factory;
    /**
     * query param service name
     * @var string
     */
    protected $queryParamClassName = 'search.query.param';
    /**
     * initialyse factory
     */
    public function __construct() {
        $this->factory = new QueryParamFactory;
    }
    /**
     * reset query conditions
     */
    public function __clone() {
        $this->reset();
    }
    
    /**
     * reset stored query params
     * @return $this
     */
    public function reset() {
        $this->storedQueryParams = [];
        return $this;
    }

     /**
      * create a new QueryParam and add it to store.
      * @param string $name
      * @param string $operator
      * @param mixed $value
      * @param boolean $andSeparator
      * @return \oat\search\base\QueryParamInterface
      */
    public function addCriterium($name, $operator, $value, $andSeparator = true) {
        $param = $this->factory
                ->setServiceLocator($this->serviceLocator)
                ->get($this->queryParamClassName , [$name, $operator, $value, $andSeparator])
                ->setParent($this);
        
        $this->storedQueryParams[] = $param;
        return $param;
    }
    /**
     * create a new QueryParam and add it to store.
     * @param string $property
     * @return \oat\search\base\QueryParamInterface
     */
    public function add($property) {
        return $this->addCriterium($property, null, null);
    }

        /**
     * return an array of \oat\search\base\QueryParamInterface
     * @return array
     */
    public function getStoredQueryParams() {
        return $this->storedQueryParams;
    }
    /**
     * change the QueryParam service name
     * @param string $queryParamsClassName
     * @return $this
     */
    public function setQueryParamClassName($queryParamsClassName) {
        $this->queryParamClassName = $queryParamsClassName;
        return $this;
    }
    /**
     * change the default QueryParam factory
     * @param FactoryAbstract $factory
     * @return $this
     */
    public function setQueryParamFactory(FactoryAbstract $factory) {
        $this->factory = $factory;
        return $this;
    }
    /**
     * return parent builder
     * @return QueryBuilderInterface
     */
    public function builder() {
        return $this->getParent();
    }
}

