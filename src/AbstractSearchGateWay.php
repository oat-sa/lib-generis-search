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

namespace oat\search;

use oat\search\base\QueryBuilderInterface;
use oat\search\base\QuerySerialyserInterface;
use oat\search\base\SearchGateWayInterface;
use oat\search\UsableTrait\DriverSensitiveTrait;
use oat\search\UsableTrait\OptionsTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * Abstract base for search gateway
 * use to manage connection to database system
 * must provide right serialyser, builder and ResultSet
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractSearchGateWay implements SearchGateWayInterface
{
    use ServiceLocatorAwareTrait;
    use OptionsTrait;
    use DriverSensitiveTrait;
    
    /**
     * serialyser service or className
     * @var string 
     */
    protected $serialyserList = [
        'taoRdf' => 'search.tao.serialyser'
    ];
    /**
     * driver escaper list
     * @var array 
     */
    protected $driverList = [
        'taoRdf' => 'search.driver.mysql'
    ];
    /**
     * driver service name
     * @var string
     */
    protected $builderClassName = 'search.query.builder';


    /**
     * resultSet service or className
     * @var string 
     */
    protected $resultSetClassName;
    /**
     * global driver name
     * @var string 
     */
    protected $driverName;
    /**
     * database connector
     * @var mixed 
     */
    protected $connector;
    /**
     * query usable by database driver
     * @var mixed 
     */
    protected $parsedQuery;

    /**
     * init the gateway
     * @return $this
     */
    public function init() {
        $options = $this->getServiceLocator()->get('search.options');
        $this->setOptions($options);
        $this->driverName = $options['driver'];
        $this->setDriverEscaper($this->getServiceLocator()->get($this->driverList[$this->driverName]));
        
        return $this;
    }
    
    /**
     * parse QueryBuilder and store parsed query
     * @param QueryBuilderInterface $Builder
     * @return $this
     */
    public function serialyse(QueryBuilderInterface $Builder) {
        $this->parsedQuery = $this->getSerialyser()->setCriteriaList($Builder)->serialyse();
        return $this;
    }

     /**
     * return configuration driver name
     * @return string
     */
    public function getDriverName() {
        return $this->driverName;
    }

    /**
     * create connector
     * @param mixed $connector
     * @return $this
     */
    public function setConnector($connector) {
        $this->connector = $connector;
        return $this;
    }
    /**
     * return database connector
     * @return mixed
     */
    public function getConnector() {
        return $this->connector;
    }
    
    /**
     * query serialyser factory
     * @return QuerySerialyserInterface
     */
    public function getSerialyser() {
       $serialyser = $this->getServiceLocator()->get($this->serialyserList[$this->driverName]);
       $serialyser->setServiceLocator($this->serviceLocator)->setDriverEscaper($this->driverEscaper)->setOptions($this->options)->prefixQuery();
       return $serialyser;
    }
    
    /**
     * change result set class name or service name
     * @param string $resultSetClassName
     * @return $this
     */
    public function setResultSetClassName($resultSetClassName) {
        $this->resultSetClassName = $resultSetClassName;
        return $this;
    }
    
     /**
     * return resultSet class name or service name
     * @return string 
     */
    public function getResultSetClassName() {
        return $this->resultSetClassName;
    }
    
    /**
     * query builder factory
     * @return QueryBuilder
     */
    public function query() {
        $builder = $this->getServiceLocator()->get($this->builderClassName);
        $builder->setOptions($this->options)->setServiceLocator($this->serviceLocator);
        return $builder;
    }
    
    /**
     * change query builder class name or service name
     * @param string $builderSetClassName
     * @return $this
     */
    public function setBuilderClassName($builderSetClassName) {
        $this->builderClassName = $builderSetClassName;
        return $this;
    }
    
    /**
     * return query builderclass name or service name
     * @return string
     */
    public function getBuilderClassName() {
        return $this->builderClassName;
    }
    
    public function getQuery() {
        return $this->parsedQuery;
    }

}
