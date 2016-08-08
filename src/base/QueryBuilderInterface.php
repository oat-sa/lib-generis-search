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

use oat\search\factory\FactoryAbstract;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
/**
 * Interface QueryBuilderInterface
 * use to create query from user data
 *
 * @package oat\search\base
 */
interface QueryBuilderInterface extends LimitableInterface, SortableInterface, OptionsInterface, ServiceLocatorAwareInterface {
    
    /**
     * change default query param className
     * @param string $queryClassName
     * @return $this
     */
    public function setQueryClassName($queryClassName);

    /**
     * change default query param factory
     * @param FactoryAbstract $factory
     * @return $this
     */
    public function setQueryFactory(FactoryAbstract $factory);

    /**
     * return query params list as array of QueryInterface
     * @return array
     */
    public function getStoredQueries();
    
    /**
     * create a new query
     * @return QueryInterface
     */
    public function newQuery();
   
    /**
     * store first QueryInterface criteria list
     * @param QueryInterface $criteria
     * @return QueryBuilderInterface
     */
    public function setCriteria(QueryInterface $criteria);
    
    /**
     * accept QueryInterface
     * and stored it.
     * @param QueryInterface $criteria
     * @return QueryBuilderInterface
     */
    public function setOr(QueryInterface $criteria);
    
}