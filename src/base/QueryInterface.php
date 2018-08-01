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

namespace oat\search\base;
use \oat\search\factory\FactoryAbstract;

/**
 * Interface QueryInterface
 * Represented criteria group must be respected
 * @package oat\search\base
 *
 * @method $this equals($value)
 * @method $this notEquals($value)
 * @method $this gt($value)
 * @method $this gte($value)
 * @method $this lt($value)
 * @method $this lte($value)
 * @method $this between(array $scope)
 * @method $this in(array $list)
 * @method $this notIn(array $list)
 * @method $this match($value)
 * @method $this notMatch($value)
 * @method $this contains($value)
 * @method $this begin($value)
 * @method $this end($value)
 * @method $this null($value)
 * @method $this notNull($value)
 */
interface QueryInterface extends OptionsInterface, ParentFluateInterface {
    
    /**
     * reset stored query params
     * @return $this
     */
    public function reset();
    /**
     * Creates a new QueryCriterion and add it to the store.
     * @param string $property
     * @return $this
     */
    public function add($property);
    /**
     * change default query param className
     * @param string $queryCriterionClassName
     * @return $this
     */
    public function setQueryCriterionClassName($queryCriterionClassName);

    /**
     * change default query param factory
     * @param FactoryAbstract $factory
     * @return $this
     */
    public function setQueryCriterionFactory(FactoryAbstract $factory);
    
    /**
     * return all query params object stored
     * @return array
     */
    public function getStoredQueryCriteria();

    /**
     * create an non stored new QueryCriterionInterface
     * @param string $property
     * @param string $operator
     * @param mixed $value
     * @return QueryCriterionInterface
     */
    public function addCriterion($property , $operator , $value);
    /**
     * return parent builder
     * @return QueryBuilderInterface
     */
    public function builder();

}

