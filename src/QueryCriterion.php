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

use oat\search\base\QueryCriterionInterface;
use oat\search\UsableTrait\ParentFluateTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * imlpemented QueryCriterion
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryCriterion
    implements QueryCriterionInterface ,
        ServiceLocatorAwareInterface 
        {
    
    use ServiceLocatorAwareTrait;
    use ParentFluateTrait;
    /**
     * property name
     * @var string
     */
    protected $name;
    /**
     * operator
     * @var string
     */
    protected $operator;
    /**
     * property value
     * @var mixed
     */
    protected $value;
    /**
     * others conditions separated by and 
     * @var array
     */
    protected $and = [];
    /**
     * others conditions separated by or 
     * @var array
     */
    protected $or = [];
    
    /**
     * return main operator if is unchanged
     * @param string|null $operator
     * @return string
     */
    protected function setDefaultOperator($operator) {
        if(is_null($operator)) {
            $operator = $this->getOperator();
        }
        return $operator;
    }

    /**
     * add a new condition on same property with AND separator
     * @param mixed $value
     * @param string|null $operator
     * @return $this
     */
    public function addAnd($value , $operator = null) {
        
        $param = new self();
        $param->setOperator($this->setDefaultOperator($operator))->setValue($value);
        $this->and[] = $param;
        return $this;
    }
    /**
     * add a new condition on same property with OR separator
     * @param mixed $value
     * @param string|null $operator
     * @return $this
     */
    public function addOr($value , $operator = null) {
        
        $param = new self();
        $param->setOperator($this->setDefaultOperator($operator))->setValue($value);
        $this->or[] = $param;
        return $this;
    }
    
    /**
     * return property name
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * return operator name
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }
    
    /**
     * return value
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * set up property name
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    /**
     * set up operator name
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator) {
        $this->operator = $operator;
        return $this;
    }
    
    /**
     * set up property value
     * @param mixed $value
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
    
    /**
     * return and
     * @return array
     */
    public function getAnd() {
       return $this->and; 
    }
    /**
     * return or
     * @return array
     */
    public function getOr() {
       return $this->or;
    }
    /**
     * set up operator and value
     * 
     * example : 
     * $this->equal('foo');
     * $this->in(1 , 2 , 3 , 4 , 5);
     * $this->between(1,10); 
     * 
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call($name, $arguments) {
        $this->operator = $name;
        
        if(empty($arguments)) {
            $this->value = '';
        } elseif(count($arguments) === 1) {
            $this->value = $arguments[0];
        } else {
            $this->value = $arguments;
        }
        
        return $this;
    }

}