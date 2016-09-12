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

namespace oat\search;

use oat\search\base\QueryInterface;
use oat\search\factory\FactoryAbstract;
use oat\search\factory\QueryFactory;
use oat\search\base\QueryBuilderInterface;
use oat\search\UsableTrait\LimitableTrait;
use oat\search\UsableTrait\OptionsTrait;
use oat\search\UsableTrait\SortableTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * implemented generic query builder
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryBuilder implements QueryBuilderInterface {
    
    use SortableTrait;
    use LimitableTrait;
    use OptionsTrait;
    use ServiceLocatorAwareTrait;
    
    /**
     * stored queries
     * array of \oat\search\base\QueryInterface
     * @var array
     */
    protected $storedQueries = [];
    /**
     * query factory
     * @var \oat\search\factory\FactoryInterface 
     */
    protected $factory;
    /**
     * query service name
     * @var string
     */
    protected $queryClassName = 'search.query.query';
    /**
     * constructor
     * initialyze queery factory
     */
    public function __construct() {
        $this->factory = new QueryFactory;
    }

    /**
     * stored queries
     * array of \oat\search\base\QueryInterface
     * @return array
     */
    public function getStoredQueries() {
        return $this->storedQueries;
    }
    
    /**
     * generate a new query 
     * @return \oat\search\base\QueryInterface
     */
    public function newQuery() {
        $factory = $this->factory;
        $factory->setServiceLocator($this->serviceLocator);
        return $factory->get($this->queryClassName)->setParent($this);
    }

     /**
     * change default Query service name
     * @param string $queryClassName
     * @return $this
     */
    public function setQueryClassName($queryClassName) {
        $this->queryClassName = $queryClassName;
        return $this;
    }
    
    /**
     * change default query factory
     * @param FactoryAbstract $factory
     * @return $this
     */
    public function setQueryFactory(FactoryAbstract $factory) {
        $this->factory = $factory;
        return $this;
    }
    
    /**
     * store first QueryInterface criteria list
     * @param QueryInterface $criteria
     * @return QueryBuilderInterface
     */
    public function setCriteria(QueryInterface $criteria) {
        $this->storedQueries[0] = $criteria;
        return $this;
    }
    
    /**
     * accept an array of QueryInterface
     * and stored it.
     * @return $this
     */
    public function setOr(QueryInterface $criteria) {
        $this->storedQueries[] = $criteria;
        return $this;
    }

}
