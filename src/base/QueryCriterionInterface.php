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

/**
 * Interface QueryCriterionInterface
 * @package oat\search\base
 */

interface QueryCriterionInterface extends ParentFluateInterface {

    /**
     * set object property on which you need to search
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * set value to search
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * set query operator
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * set `and` condition.
     * if operator is null parent operator is use
     * use full for array properties
     *
     * for example test.id = [1 , 12 , 50]
     * test.id contain 1 and test.id contain 12
     *
     * @param mixed $value
     * @param null|string $operator
     * @return $this
     */
    public function addAnd($value , $operator = null);

    /**
     * set `or` condition.
     * if operator is null parent operator is use
     * for example : name equal 'christophe' or name begin by 'b'
     *
     * @param mixed $value
     * @param null|string $operator
     * @return $this
     */
    public function addOr($value , $operator = null);

    /**
     * return param name
     * @return string
     */
    public function getName();

    /**
     * return array of possible values
     * @return array
     */
    public function getValue();

    /**
     * return array of possible operators
     * @return mixed
     */
    public function getOperator();
    
    /**
     * return an array of QueryCriterionInterface stored for 'or' condition
     * @return array
     */
    public function getOr();
    
    /**
     * return an array of QueryCriterionInterface stored for 'and' condition
     * @return array
     */
    public function getAnd();
    
}