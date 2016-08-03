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

use oat\search\base\QueryBuilderInterface;
use oat\search\base\QueryCriterionInterface;
use oat\search\factory\FactoryAbstract;
use oat\search\factory\QueryCriterionFactory;
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
    protected $storedQueryCriteria = [];
    /**
     * default QueryCriterion factory
     * @var \oat\search\factory\FactoryInterface
     */
    protected $factory;
    /**
     * query param service name
     * @var string
     */
    protected $queryCriterionClassName = 'search.query.param';
    /**
     * initialyse factory
     */
    public function __construct() {
        $this->factory = new QueryCriterionFactory;
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
        $this->storedQueryCriteria = [];
        return $this;
    }

    /**
     * create a new QueryCriterion and add it to store.
     * @param string $property
     * @return \oat\search\base\QueryCriterionInterface
     */
    public function add($property) {
        return $this->addCriterion($property , null , null);
    }

        /**
     * return an array of \oat\search\base\QueryCriterionInterface
     * @return array
     */
    public function getStoredQueryCritera() {
        return $this->storedQueryCriteria;
    }
    /**
     * change the QueryCriterion service name
     * @param string $queryParamsClassName
     * @return $this
     */
    public function setQueryCriterionClassName($queryParamsClassName) {
        $this->queryCriterionClassName = $queryParamsClassName;
        return $this;
    }
    /**
     * change the default QueryCriterion factory
     * @param FactoryAbstract $factory
     * @return $this
     */
    public function setQueryCriterionFactory(FactoryAbstract $factory) {
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

    /**
     * @inheritdoc
     */
    public function addCriterion($property , $operator , $value)
    {
        $factory = $this->factory;
        $factory->setServiceLocator($this->serviceLocator);
        return $factory->get(
            $this->queryCriterionClassName,
            [$property , $operator , $value]
        )->setParent($this);
    }

}

