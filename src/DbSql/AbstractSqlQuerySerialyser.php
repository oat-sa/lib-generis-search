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

namespace oat\search\DbSql;

use oat\search\base\exception\QueryParsingException;
use \oat\search\AbstractQuerySerialyser;
/**
 * transform QueryBuilder criteria to an exploitable query
 * for database system driver
 * 
 * Abstract base for SQL Query Serialyser
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractSqlQuerySerialyser extends AbstractQuerySerialyser {
    
    /**
     * create base query for SQL single table query
     * @return $this
     */
    public function prefixQuery() {
        
        $options = $this->getOptions();
        
        if ($this->validateOptions($options)) {
            $this->queryPrefix = $this->getDriverEscaper()->dbCommand('SELECT') . ' ';
            $fields = $this->setFieldList($options);
            $this->queryPrefix .= $fields . ' ' . $this->getDriverEscaper()->dbCommand('FROM') . ' ' . $this->getDriverEscaper()->reserved($options['table']) . ' ' . $this->getDriverEscaper()->dbCommand('WHERE') . ' ';
        }
        
        return $this;
    }
    /**
     * set up selected field list
     * @param array $options
     * @return string
     */
    protected function setFieldList(array $options) {
        $fields = $this->getDriverEscaper()->getAllFields();
        if (array_key_exists('fields', $options)) {
            $fieldsList = [];
            foreach ($options['fields'] as $field) {
                $fieldsList[] = $this->getDriverEscaper()->reserved($field);
            }
            $fields = implode(' ' . $this->getDriverEscaper()->getFieldsSeparator() . ' ', $fieldsList);
        }
        return $fields;
    }

    /**
     * verify options requirements
     * @param array $options
     * @return boolean
     * @throws QueryParsingException
     */
    protected function validateOptions(array $options) {

        if (!array_key_exists('table', $options)) {
            throw new QueryParsingException('table option is mandatory');
        }
        return true;
    }

    /**
     * call before operation concatenation
     * @return $this
     */
    protected function prepareOperator() {
        $this->query .= $this->operationSeparator;
        return $this;
    }

    /**
     * add a new criterium to query
     * @param string $expression
     * @return $this
     */
    protected function addOperator($expression) {
        $this->query .= $expression;
        return $this;
    }

    /**
     * add operation separator
     * @param boolean $and
     * @return $this
     */
    protected function addSeparator($and) {
        $separator = $this->getDriverEscaper()->dbCommand('OR');

        if ($and) {
            $separator = $this->getDriverEscaper()->dbCommand('AND');
        }
        $this->query .= ' ' . $separator . ' ';
        return $this;
    }

    /**
     * create limit part for query
     * @param integer $limit
     * @param integer $offset
     * @return string
     */
    protected function addLimit($limit, $offset = null) {
        $limitQuery = '';

        if (intval($limit) > 0) {
            $limitQuery .= $this->getDriverEscaper()->dbCommand('LIMIT') . ' ' . $limit;  
            if (!is_null($offset)) {
                $limitQuery .= ' ' . $this->getDriverEscaper()->dbCommand('OFFSET') . ' ' . $offset;
            }
        }
        

        return $limitQuery;
    }

    /**
     * parse sort criteria
     * @param array $sortCriteria
     * @return string
     */
    protected function addSort(array $sortCriteria) {
        $sort = '';

        $orderOperator = [
            'asc' => $this->getDriverEscaper()->dbCommand('ASC'),
            'desc' => $this->getDriverEscaper()->dbCommand('DESC'),
        ];

        if (count($sortCriteria) > 0) {
            $sort = $this->getDriverEscaper()->dbCommand('ORDER BY');
            $crirerias = [];
            
            foreach ($sortCriteria as $field => $order) {
                if(array_key_exists($order, $orderOperator)) {
                    $crirerias[] = ' ' . $this->getDriverEscaper()->reserved($field) . ' ' . $orderOperator[$order] . ' ';
                } else {
                    throw new QueryParsingException();
                }
            }
            $sort .= implode($this->getDriverEscaper()->getFieldsSeparator(), $crirerias);
        }

        return $sort;
    }
    /**
     * finish an close query
     * @return $this
     */
    protected function finishQuery() {
        $this->query .= $this->addSort($this->criteriaList->getSort()) . ' ' . $this->addLimit($this->criteriaList->getLimit(), $this->criteriaList->getOffset())  . $this->operationSeparator;
        return $this;
    }

}
