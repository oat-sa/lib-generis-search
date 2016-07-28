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

use \oat\taoSearch\model\search\QueryParserInterface;
use \oat\taoSearch\model\search\QueryBuilderInterface;
use \oat\taoSearch\model\search\QueryInterface;
use oat\taoSearch\model\search\QueryParamInterface;
use \oat\taoSearch\model\search\exception\QueryParsingException;
use \oat\taoSearch\model\search\UsableTrait\DriverSensitiveTrait;
use oat\taoSearch\model\search\UsableTrait\OptionsTrait;
use \Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * Query parser are use to transform 
 * QueryBuilder to an exploitable query ofr database driver
 * @author christophe GARCIA
 */
abstract class AbstractQueryParser implements QueryParserInterface {
    
    use DriverSensitiveTrait;
    use OptionsTrait;
    use ServiceLocatorAwareTrait;
    /**
     * parsed query
     * @var string
     */
    protected $query;
    /**
     * query builder to parse for query generation
     * @var QueryBuilderInterface 
     */
    protected $criteriaList;
    /**
     * supported operator class as
     * [name => classname ]
     * @var array list of supported operators 
     */
    protected $supportedOperators = [];
    /**
     * next separator to add
     * @var string 
     */
    protected $nextSeparator;
    /**
     * generated query prefix
     * @var string
     */
    protected $queryPrefix;
    /**
     * nameSapce for operator class name
     * @var string 
     */
    protected $operatorNameSpace;
    /**
     * pretty print char
     * @var string
     */
    protected $prettyChar = "\n";
    /**
     * not pretty print char
     * @var string
     */
    protected $unPrettyChar = ' ';
    /**
     * place between each operation
     * @var string
     */
    protected $operationSeparator = ' ';
    /**
     * change operation separator
     * to pretty print or unpretty print
     * @param boolean $pretty
     * @return $this
     */
    public function pretty($pretty) {
        if($pretty) {
            $this->operationSeparator = $this->prettyChar ;
        } else {
            $this->operationSeparator = $this->unPrettyChar ;
        }
        return $this;
    }
    
    /**
     * set QueryBuilder to parse
     * @param QueryBuilderInterface $criteriaList
     * @return $this
     */
    public function setCriteriaList(QueryBuilderInterface $criteriaList) {
        $this->criteriaList = $criteriaList;
        return $this;
    }
    /**
     * generate query exploitable by driver
     * @return string
     */
    public function parse() {
        
        $this->query = $this->queryPrefix;
        
        foreach ($this->criteriaList->getStoredQueries() as $query) {
            $this->parseQuery($query);
        }
        
        $this->finishQuery();
        return $this->query;
    }
    
    /**
     * parse QueryInterface criteria
     * @param QueryInterface $query
     * @return $this
     */
    protected function parseQuery(QueryInterface $query) {
        foreach ($query->getStoredQueryParams() as $operation) {
            $this->parseOperation($operation);
        }
        return $this;
    }

    /**
     * parse QueryParamInterface criteria
     * @param QueryParamInterface $operation
     * @return $this
     */
    protected function parseOperation(QueryParamInterface $operation) {
        
        $operation->setValue($this->getOperationValue($operation->getValue()));
        
        $this->setNextSeparator($operation->getSeparator())
                ->prepareOperator();

        $command = $this->getOperator($operation->getOperator())->convert($operation);
        
        $this->setConditions($command , $operation->getAnd(), 'and');
        $this->setConditions($command , $operation->getOr(), 'or');
        $this->addOperator($command);
        
        return $this;
        
    }
    
    /**
     * convert value to string if it's an object
     * @param mixed $value
     * @return string
     */
    protected function getOperationValue($value) {
        
        if(is_a($value, '\\oat\\taoSearch\\model\\search\\QueryBuilderInterface')) {
            $parser = clone $this;
            $value = $parser->setCriteriaList($value)->parse();
        } else if(is_a($value, '\\oat\\taoSearch\\model\\search\\QueryInterface')) {
            $value = $this->parseQuery($value);
        }
        return $value;
    }


    /**
     * generate and add to query a condition 
     * exploitable by database driver
     * @param type $command
     * @param array $conditionList
     * @param type $separator
     * @return string
     */
    protected function setConditions(&$command , array $conditionList , $separator = 'and') {
        foreach($conditionList as $condition) {
            
            $addCondition = $this->getOperator($condition->getOperator())->convert($condition);
            $this->mergeCondition($command , $addCondition , $separator);
            
        }
        return $command;
    }

     /**
      * operator command factory 
      * 
      * @param type $operator
      * 
      * @return \oat\taoSearch\model\search\command\OperatorConverterInterface
      * @throws QueryParsingException
      */
    protected function getOperator($operator) {
         /**
          * @todo change that for a factory
          */
        if(array_key_exists($operator, $this->supportedOperators)) {
            
            $operatorClass = $this->operatorNameSpace . '\\' . ($this->supportedOperators[$operator]);
            $operator = $this->getServiceLocator()->get($operatorClass);
            $operator->setDriverEscaper($this->getDriverEscaper());
            return $operator;
        }
        throw new QueryParsingException('this driver doesn\'t support ' . $operator . ' operator');
    }
    
    /**
     * change next separator to "and" or "or"
     * @param boolean $and
     * @return $this
     */
    protected function setNextSeparator($and) {
        if(!is_null($this->nextSeparator)) {
            
            $this->addSeparator($this->nextSeparator);
        }
        $this->nextSeparator = $and;
        return $this;
    }
    
    /**
     * change your merge process
     * merge array, concat string, fetch object .....
     * 
     * @param mixed $command main query 
     * @param mixed $condition condition to merger
     * @param mixed|null $separator
     * @return $this;
     */
    abstract protected function mergeCondition(&$command , $condition, $separator = null);
    
    /**
     * generate the beginning of query
     * @param array $options
     * @return $this;
     */
    abstract public  function prefixQuery();
    
    /**
     * prepare query to receive new condition
     * @return $this;
     */
    abstract protected  function prepareOperator();
    
    /**
     * add new Condition
     * @param string $expression
     * @return $this;
     */
    abstract protected  function addOperator($expression);
    
    /**
     * add new condition separator
     * @param boolean $and
     * @return $this
     */
    abstract protected function addSeparator($and);
    /**
     * parse limitable queries
     * @param integer $limit
     * @param integer|null $offset
     * @return mixed
     */
    abstract protected function addLimit($limit, $offset = null);
    
    /**
     * parse sort criteria
     * @param array $sortCriteria
     * @return mixed
     */
    abstract protected function addSort(array $sortCriteria);
    /**
     * close query
     * @return $this
     */
    abstract protected function finishQuery();
    
}
