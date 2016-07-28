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

namespace oat\taoSearch\model\searchImp;

use oat\taoSearch\model\factory\FactoryAbstract;
use oat\taoSearch\model\factory\QueryFactory;
use oat\taoSearch\model\search\QueryBuilderInterface;
use oat\taoSearch\model\search\UsableTrait\LimitableTrait;
use oat\taoSearch\model\search\UsableTrait\OptionsTrait;
use oat\taoSearch\model\search\UsableTrait\SortableTrait;
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
     * array of \oat\taoSearch\model\search\QueryInterface
     * @var array
     */
    protected $storedQueries = [];
    /**
     * query factory
     * @var \oat\taoSearch\model\factory\FactoryInterface 
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
     * array of \oat\taoSearch\model\search\QueryInterface
     * @return array
     */
    public function getStoredQueries() {
        return $this->storedQueries;
    }
    
    /**
     * generate a new query 
     * @return \oat\taoSearch\model\search\QueryInterface
     */
    public function newQuery() {
        $factory = $this->factory;
        $factory->setServiceLocator($this->serviceLocator);
        return $factory->get($this->queryClassName)->setParent($this);
    }
    
    /**
     * create a new query and store it
     * @return QueryInterface
     */
    public function criteria() {
        $criteria = $this->newQuery();
        $this->storedQueries[] = $criteria;
        return $criteria;
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

}
