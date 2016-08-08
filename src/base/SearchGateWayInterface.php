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

use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\Query\DriverSensitiveInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Interface SearchGateWayInterface
 *
 * use to manage connection to database system
 * must provide right serialyser, builder and ResultSet
 * 
 * @package oat\search\base
 */
interface SearchGateWayInterface extends OptionsInterface, DriverSensitiveInterface, ServiceLocatorAwareInterface
{
    /**
     * intitialyse your gateway
     */
    public function init();

    /**
     * set up database connector if needed
     * @param mixed connector
     * @return $this
     */
    public function setConnector($connector);
    
    /**
     * return database connector
     * @return mixed
     */
    public function getConnector();

     /**
     * try to connect to database. throw an exception
     * if connection failed.
     *
     * @throws SearchGateWayExeption
     * @return $this
     */
    public function connect();

    /**
     * send a searchQuery and return a resultSetOnSuccess
     * throws a exception on failure
     *
     * @throws SearchGateWayExeption
     * @return ResultSetInterface
     */
    public function search(QueryBuilderInterface $Builder);
    
    /**
     * print parsed query
     * @return $this
     */
    public function printQuery();
    
    /**
     * return query
     * @return string
     */
    public function getQuery();

    /**
     * parse QueryBuilder and store parsed query
     * @param \oat\search\base\QueryBuilderInterface $Builder
     * @return $this
     */
    public function serialyse(QueryBuilderInterface $Builder);

        /**
     * set up a new serialyser
     * @return QuerySerialyserInterface
     */
    public function getSerialyser();

     /**
      * return GateWay DriverName
     * @return string  
     */
    public function getDriverName();

    
    /**
     * create a new Query builder
     * @return QueryBuilderInterface
     */
    public function query();
    
    /**
     * change default resultSet service alias or class name
     * 
     * @param string $resultSetClassName
     * @return $this
     */
    public function setBuilderClassName($resultSetClassName);
    
    /**
     * return resultSet service alias or class name
     * @return string
     */
    public function getBuilderClassName();
    
     /**
     * change default resultSet service alias or class name
     * 
     * @param string $resultSetClassName
     * @return $this
     */
    public function setResultSetClassName($resultSetClassName);
    
    /**
     * return resultSet service alias or class name
     * @return string
     */
    public function getResultSetClassName();
    
    /**
     * return query total count result
     * @return integer
     */
    public function count(QueryBuilderInterface $Builder);
    
}