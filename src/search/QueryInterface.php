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

namespace oat\taoSearch\model\search;
use \oat\taoSearch\model\factory\FactoryAbstract;
/**
 * create query
 */
interface QueryInterface extends OptionsInterface, ParentFluateInterface {
    
    /**
     * reset stored query params
     * @return $this
     */
    public function reset();
    /**
     * change default query param className
     * @param string $queryParamsClassName
     * @return $this
     */
    public function setQueryParamClassName($queryParamsClassName);

    /**
     * change default query param factory
     * @param callable $factory
     * @return $this
     */
    public function setQueryParamFactory(FactoryAbstract $factory);
    
     /**
     * create and store a new QueryParamInterface
     * @param string $name
     * @param string $operator
     * @param mixed $value
     * @param bool $andSeparator true for and , false for or
     * @return QueryParamInterface
     */
    public function addOperation($name , $operator , $value , $andSeparator = true);
    
    /**
     * return all query params object stored
     * @return array
     */
    public function getStoredQueryParams();
    
    /**
     * return parent builder
     * @return QueryBuilderInterface
     */
    public function builder();
}

