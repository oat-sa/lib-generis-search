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

use oat\search\base\Query\DriverSensitiveInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Interface QuerySerialyserInterface
 *
 * transform QueryBuilder criteria to an exploitable query
 * for database system driver
 *
 * @package oat\search\base
 */
interface QuerySerialyserInterface extends DriverSensitiveInterface, OptionsInterface, ServiceLocatorAwareInterface {

     /**
     * create query base
     * @param array $options
     * @return $this
     */
    public function prefixQuery();
    
    /**
     * set or unset pretty print
     * @param boolean $pretty
     * @return $this
     */
    public function pretty($pretty);

        /**
     * transform QueryBuilderInterface to an exploitable criteria list
     *
     * @param QueryBuilderInterface $criteriaList
     * @return $this
     */
    public function setCriteriaList(QueryBuilderInterface $criteriaList);

    /**
     * create query as string, array, or other exploitable by data storage driver.
     * using command design pattern
     *
     * @internal OperatorConverterInterface $converter
     * @return mixed
     */
    public function serialyse();

}