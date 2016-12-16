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

namespace oat\search\DbSql\TaoRdf;

use \oat\search\DbSql\AbstractSqlQuerySerialyser;
use \oat\search\helper\SupportedOperatorHelper;
/**
 * Tao RDF Onthology serialyser
 * 
 * transform QueryBuilder criteria to an exploitable query
 * for database system driver
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QuerySerialyser extends AbstractSqlQuerySerialyser {
    /**
     * namespace for operator converter class
     * @var string 
     */
     protected $operatorNameSpace = '\\oat\\search\\DbSql\\TaoRdf\\Command';
     /**
      * language query part
      * @var string
      */
     protected $language = '';


     /**
      * suppoted operator class name
      * @var array
      */
     protected $supportedOperators = 
             [
                SupportedOperatorHelper::EQUAL => 'Equal',
                SupportedOperatorHelper::DIFFERENT => 'NotEqual',
                SupportedOperatorHelper::GREATER_THAN => 'GreaterThan',
                SupportedOperatorHelper::LESSER_THAN => 'LesserThan',
                SupportedOperatorHelper::GREATER_THAN_EQUAL => 'GreaterThanOrEqual',
                SupportedOperatorHelper::LESSER_THAN_EQUAL => 'LesserThanOrEqual',
                SupportedOperatorHelper::CONTAIN => 'LikeContain',
                SupportedOperatorHelper::MATCH => 'Like',
                SupportedOperatorHelper::NOT_MATCH => 'NotLike',
                SupportedOperatorHelper::IN => 'In',
                SupportedOperatorHelper::BETWEEN => 'Between',
                SupportedOperatorHelper::BEGIN_BY => 'LikeBegin',
                SupportedOperatorHelper::ENDING_BY => 'LikeEnd',
                SupportedOperatorHelper::NOT_IN => 'NotIn',
                SupportedOperatorHelper::IS_NULL => 'IsNULL',
                SupportedOperatorHelper::IS_NOT_NULL => 'IsNotNull',
             ];
    
     /**
      * create query begining
      * @return $this
      */
    public function prefixQuery()
    {
        $options = $this->getOptions();
        
        if($this->validateOptions($options)) {
            
            if(array_key_exists('language' , $options)) {
               $this->language = $this->setLanguageCondition($options['language'], true);
            }
            
            $this->queryPrefix = $this->initQuery();
        }
        return $this;

    } 
    
    /**
     * query base
     * @param type $fields
     * @param type $languageEmpty
     * @param type $languageStrict
     * @return string
     */
    protected function initQuery() {
        
        $table = $this->getOptions()['table'];
        
        return $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    $this->getDriverEscaper()->dbCommand('DISTINCT') .'(' .
                    $this->getDriverEscaper()->reserved('subject') . ')' . ' ' . 
                    $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    $this->getDriverEscaper()->reserved($table) . ' ' .
                    $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('WHERE') . ' ' .
                    $this->getDriverEscaper()->reserved('subject') . ' ' .
                    $this->getDriverEscaper()->dbCommand('IN') . 
                    $this->operationSeparator . '(' .
                    $this->getDriverEscaper()->dbCommand('SELECT') .  ' ' .
                    $this->getDriverEscaper()->reserved('subject') .  ' ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    $this->operationSeparator . '(' .
                    $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . 
                    $this->getDriverEscaper()->dbCommand('DISTINCT') .'(' .
                    $this->getDriverEscaper()->reserved('subject') . ')' . ' ' . 
                    $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' . 
                    $this->getDriverEscaper()->reserved($table) . ' ' .
                    $this->getDriverEscaper()->dbCommand('WHERE') .  
                    $this->operationSeparator ;
    }

     /**
     * return an SQL string with language filter condition
     * 
     * @param string $language
     * @param boolean $emptyAvailable
     * @return string
     */
    public function setLanguageCondition($language , $emptyAvailable = false) {
        $languageField = $this->getDriverEscaper()->reserved('l_language');
        $languageValue      = $this->getDriverEscaper()->escape($language);
        $sql = '('; 
        $sql .= $languageField .' = ' . $this->getDriverEscaper()->quote($languageValue) . ''; 
        if($emptyAvailable) {
            $sql .= ' ' . $this->getDriverEscaper()->dbCommand('OR') . ' ' . $languageField . ' = ' . $this->getDriverEscaper()->getEmpty();
        }
        $sql .= ') ' . $this->getDriverEscaper()->dbCommand('AND') . $this->operationSeparator;
        return $sql;
    }
     
    /**
     * create sub query to add a new condition to search predicates values
     * @param string $expression
     * @return $this
     */
    public function addOperator($expression)
    {
       $this->query .= '( ' . $this->getDriverEscaper()->reserved('subject') . ' ' . 
               $this->getDriverEscaper()->dbCommand('IN') . 
               $this->operationSeparator .'(' . 
               $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . 
               $this->getDriverEscaper()->dbCommand('DISTINCT') . ' ' . 
               $this->getDriverEscaper()->reserved('subject') . ' ' .
               $this->getDriverEscaper()->dbCommand('FROM') .' ' . 
               $this->getDriverEscaper()->reserved($this->options['table']) . ' ' .
               $this->getDriverEscaper()->dbCommand('WHERE') . 
               $this->operationSeparator . $this->language . 
               $this->operationSeparator .$expression . ')' .
               ')';
       return $this;
    }
    /**
     * merge multiple condition QueryCriterion
     * @param string $command
     * @param string $condition
     * @param string $separator
     * @return $this
     */
    protected function mergeCondition(&$command , $condition, $separator = null) {
        
        $command .= (is_null($separator))? '' : ' ' . $this->getDriverEscaper()->dbCommand($separator);
        $command .= ' ' . $condition . $this->operationSeparator;
        
        return $this;
    }
    /**
     * class query
     * @return $this
     */
    protected function finishQuery() {
        
        $this->query .= ' ' . $this->addLimit($this->criteriaList->getLimit() , $this->criteriaList->getOffset()) .' ) ' . 
                $this->operationSeparator .
                $this->getDriverEscaper()->dbCommand('AS') .
                ' subQuery ) ' . $this->operationSeparator . 
                $this->addSort($this->criteriaList->getSort());
        
        return $this;
    }
    
}
