<?php

/*
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

namespace oat\search\DbSql\TaoRdf;

use oat\search\base\exception\QueryParsingException;
use oat\search\helper\SupportedOperatorHelper;
use oat\search\DbSql\AbstractSqlQuerySerialyser;

/**
 * Tao RDF Onthology serialyser
 * 
 * transform QueryBuilder criteria to an exploitable query
 * for database system driver
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class UnionQuerySerialyser extends AbstractSqlQuerySerialyser {

    /**
     * namespace for operator converter class
     * @var string 
     */
    protected $operatorNameSpace = '\\oat\\search\\DbSql\\TaoRdf\\Command';

    /**
     * user language query part
     * @var string
     */
    protected $userLanguage = '';

    /**
     * flag to generate count query
     * @var boolean
     */
    protected $count = false;

    /**
     * default language query part 
     * used in order if user language isn't set 
     * @var string
     */
    protected $defaultLanguage = '';

    protected $predicateLoop = 0;

    /**
     * readables model ID
     * @var \core_kernel_persistence_smoothsql_SmoothModel
     */
    protected $model = null;

    /**
     * suppoted operator class name
     * @var array
     */
    protected $supportedOperators = [
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
    public function prefixQuery() {
        $options = $this->getOptions();

        if ($this->validateOptions($options)) {

            if (array_key_exists('language', $options)) {
                $this->userLanguage = $this->setLanguageCondition($options['language'], true);
            }
            if (array_key_exists('defaultLanguage', $options)) {
                $this->defaultLanguage = $this->setLanguageCondition($options['defaultLanguage'], true);
            }
            if (array_key_exists('model', $options)) {
                $this->model = $options['model'];
            }
            $this->queryPrefix = $this->initQuery();
        }
        return $this;
    }

    /**
     * set count
     * @param boolean $count
     * @return \oat\search\DbSql\TaoRdf\UnionQuerySerialyser
     */
    public function count($count = true) {
        $this->count = $count;
        return $this;
    }

    /**
     * query base
     * @return string
     */
    protected function initQuery() {
        /**
         * SELECT subject FROM (
         */
        return $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                '(';
    }

    /**
     * return an SQL string with language filter condition
     * 
     * @param string $language
     * @param boolean $emptyAvailable
     * @return string
     */
    public function setLanguageCondition($language, $emptyAvailable = false) {
        $languageField = $this->getDriverEscaper()->reserved('l_language');
        $languageValue = $this->getDriverEscaper()->escape($language);
        $sql = '(';
        $sql .= $languageField . ' = ' . $this->getDriverEscaper()->quote($languageValue) . '';
        if ($emptyAvailable) {
            $sql .= ' ' . $this->getDriverEscaper()->dbCommand('OR') . ' ' . $languageField . ' = ' . $this->getDriverEscaper()->getEmpty();
        }
        $sql .= ') ' . $this->getDriverEscaper()->dbCommand('AND');
        return $sql;
    }

    /**
     * create sub query to add a new condition to search predicates values
     * @param string $expression
     * @return $this
     */
    public function addOperator($expression) {
        /**
         * SELECT DISTINCT subject FROM statements WHERE
         */
        $this->query .= '(' .
                $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->dbCommand('DISTINCT') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                $this->getDriverEscaper()->reserved($this->options['table']) . ' ' .
                $this->getDriverEscaper()->dbCommand('WHERE') .
                $this->operationSeparator .
                $this->userLanguage . ' ' . $expression;

        if (!empty($this->model)) {
            $this->query .=  $this->getDriverEscaper()->dbCommand('AND') . ' '.
                $this->getDriverEscaper()->reserved('modelid') . ' '.
                $this->getDriverEscaper()->dbCommand('IN') . ' '.
                '(' . implode(',', $this->model->getReadableModels()) . ')'.
                $this->operationSeparator ;
        }
        $this->query .= ' )'.')';

        $this->predicateLoop ++;

        return $this;
    }

    /**
     * merge multiple condition QueryCriterion
     * @param string $command
     * @param string $condition
     * @param string $separator
     * @return $this
     */
    protected function mergeCondition(&$command, $condition, $separator = null) {

        $command .= (is_null($separator)) ? '' : ' ' . $this->getDriverEscaper()->dbCommand($separator);
        $command .= ' ' . $condition . $this->operationSeparator;

        return $this;
    }

    /**
     * add operation separator
     * @param boolean $and
     * @return $this
     */
    protected function addSeparator($and) {
        if ($and) {
            $separator = $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('UNION') . ' ' .
                    $this->getDriverEscaper()->dbCommand('ALL') .
                    $this->operationSeparator;
        } else {
            $separator = $this->closeOperation() .
                    $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('UNION') .
                    $this->operationSeparator .
                    $this->queryPrefix;

            $this->predicateLoop = 0;
        }
        $this->query .= ' ' . $separator . ' ';
        return $this;
    }

    protected function closeOperation() {

        $sql = ') '.
                $this->getDriverEscaper()->dbCommand('AS') . ' unionq' .
                $this->operationSeparator .
                $this->getDriverEscaper()->dbCommand('GROUP') . ' ' .
                $this->getDriverEscaper()->dbCommand('BY') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('HAVING') . ' ' .
                $this->getDriverEscaper()->dbCommand('count(*)') . ' ' .
                '>=' . $this->predicateLoop;

        return $sql;
    }

    /**
     * add prefix if query is sorted
     * @param array $aliases
     * @return string
     */
    protected function sortedQueryPrefix(array $aliases) {

        $sortFields = [];

        $result = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') .
                $this->operationSeparator . '(' .
                $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->reserved('mainq') . '.' .
                $this->getDriverEscaper()->reserved('subject') .
                $this->getDriverEscaper()->getFieldsSeparator();

        foreach ($aliases as $alias) {
            $sortFields[] = $this->getDriverEscaper()->reserved($alias['name']) . '.' .
                    $this->getDriverEscaper()->reserved('object') . ' ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' . $alias['name'];
        }

        $result .= implode($this->getDriverEscaper()->getFieldsSeparator(), $sortFields)
                . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                ' ( ' . $this->query . ' ) AS mainq ' .
                $this->operationSeparator;

        return $result;
    }

    /**
     * return Order by string
     * @param array $aliases
     * @return string
     */
    protected function orderByPart(array $aliases) {
        $sortFields = [];

        foreach ($aliases as $alias) {
            $sortFields[] = $this->getDriverEscaper()->reserved($alias['name']) . '.' .
                    $this->getDriverEscaper()->reserved('object') . ' ' .
                    $alias['dir'] . $this->operationSeparator;
        }

        $result = $this->getDriverEscaper()->dbCommand('ORDER BY') . ' ' .
                implode($this->getDriverEscaper()->getFieldsSeparator(), $sortFields)
                . $this->operationSeparator;

        return $result;
    }

    protected function joinOrderQuery(array $aliases) {
        $table = $this->getDriverEscaper()->reserved($this->options['table']);
        $sortFields = [];
        $language = empty($this->userLanguage) ? $this->defaultLanguage : $this->userLanguage;
        foreach ($aliases as $alias) {
            $sortFields[] = $this->operationSeparator .
                    $this->getDriverEscaper()->dbCommand('LEFT') . ' ' .
                    $this->getDriverEscaper()->dbCommand('JOIN') . ' (' .
                    $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    $this->getDriverEscaper()->reserved('subject') .
                    $this->getDriverEscaper()->getFieldsSeparator() .
                    $this->getDriverEscaper()->reserved('object') . ' ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    $table . ' ' .
                    $this->getDriverEscaper()->dbCommand('WHERE') . ' ' .
                    $language . ' ' .
                    $this->getDriverEscaper()->reserved('predicate') . ' = ' .
                    $this->getDriverEscaper()->quote($alias['predicate']) . ' ) ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                    $alias['name'] . ' ' . $this->getDriverEscaper()->dbCommand('ON') . ' ( ' .
                    $this->getDriverEscaper()->reserved('mainq') . '.' .
                    $this->getDriverEscaper()->reserved('subject') .
                    ' = ' . $this->getDriverEscaper()->reserved($alias['name']) . '.' .
                    $this->getDriverEscaper()->reserved('subject') . ') ';
        }

        return implode($this->operationSeparator, $sortFields);
    }
    
    protected function setSortQuery(array $sortCriteria , $orderOperator) {
        
        $sort = '';
        $aliases = [];
        $index = 1;
        
        if (count($sortCriteria) > 0) {

            foreach ($sortCriteria as $field => $order) {
                if (!array_key_exists($order, $orderOperator)) {
                    throw new QueryParsingException('unknow sort order ' . $order . ' ');
                }
                $aliases[] = [
                            'name' => 'orderq' . $index,
                            'predicate' => $field,
                            'dir' => $order,
                ];
                $index++;
            }

            $sort .= $this->sortedQueryPrefix($aliases);
            $sort .= $this->joinOrderQuery($aliases);
            $sort .= $this->orderByPart($aliases);
            $sort .= ') ' . $this->getDriverEscaper()->dbCommand('AS') .
                    ' rootq' . $this->operationSeparator;

            $this->query = $sort;
            return $sort;
        }
        
    }
    
    /**
     * set sort as random
     * @return string
     */
    protected function addRandomSort() {
        $random = '';
        
        $this->query .= $this->operationSeparator .
                $this->getDriverEscaper()->dbCommand('ORDER BY') . ' ' .
                $this->getDriverEscaper()->random() . 
                $this->operationSeparator;
        
        return $random;
    }

     /**
     * parse sort criteria
     * @param array $sortCriteria
     * @return string
     */
    protected function addSort(array $sortCriteria) {
        
        if($this->criteriaList->getRandom()) {
            $sort = $this->addRandomSort();
        } else {
            $orderOperator = [
                'asc' => $this->getDriverEscaper()->dbCommand('ASC'),
                'desc' => $this->getDriverEscaper()->dbCommand('DESC'),
            ];
            $sort = $this->setSortQuery($sortCriteria, $orderOperator);
        }
        
        return $sort;
    }

    /**
     * call before operation concatenation
     * @return $this
     */
    protected function prepareOperator() {
        $this->query .= ' ';
        return $this;
    }

    /**
     * class query
     * @return $this
     */
    protected function finishQuery() {
        $this->query .= $this->closeOperation();

        if ($this->count) {
            $this->query = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    'COUNT(*)' . ' ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' cpt ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    '(' . $this->query . ') ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' rootq ';
        } else {
            $this->addSort($this->criteriaList->getSort());
            $this->query .= $this->operationSeparator . $this->addLimit($this->criteriaList->getLimit(), $this->criteriaList->getOffset());
        }

        $this->predicateLoop = 0;
        return $this;
    }

}
